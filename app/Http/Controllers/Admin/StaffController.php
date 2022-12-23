<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCustomerRequest;
use App\Http\Requests\Admin\StoreStaffRequest;
use App\Http\Requests\Admin\StoreWalletTransactionRequest;
use App\Http\Requests\Admin\UpdateCustomerRequest;
use App\Http\Requests\Admin\UpdateStaffRequest;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\CustomerWalletTransaction;
use App\Models\Department;
use App\Models\OrderItem;
use App\Models\Staff;
use App\Models\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StaffController extends Controller
{
    private $title = null;

    public function __construct()
    {
        $this->middleware('permission:staff_list|staff_create|staff_edit|staff_delete', ['only' => ['index', 'show', 'getData']]);
        $this->middleware('permission:staff_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:staff_wallet_transaction', ['only' => ['wallet_transaction', 'store_wallet_transaction']]);

        $this->title = 'Staff Management';
    }
    public function index(Request $request)
    {
        $title = $this->title;
        $breadcrumbs = ['Staff' => route('admin.staffs.index')];
        return view('admin.staffs.index', compact('title', 'breadcrumbs'));
    }

    public function create()
    {
        $title = $this->title;
        $breadcrumbs = ['Staff' => route('admin.staffs.index'), 'Create' => '#'];
        $departments = Department::get();
        $code_no = $this->getCodeNo();

        return view('admin.staffs.create', compact('title', 'breadcrumbs', 'departments', 'code_no'));
    }

    public function store(StoreStaffRequest $request)
    {
        $customer_type = CustomerType::where('name', 'Staff')->first();
        $customer_type_id = $customer_type ? $customer_type->id : null;

        DB::beginTransaction();
        try {
            $customer =  Customer::create([
                'name' => $request->name,
                'customer_type_id' => $customer_type_id,
                'phone_no' => $request->phone_no,
                'is_staff' => 1,
                'status' => 1,
            ]);
            Staff::create([
                'customer_id' => $customer->id,
                'department_id' => $request->department_id,
                'code' => $request->code,
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
        DB::commit();

        if (isset($request->new)) {
            return redirect()->route('admin.staffs.create')->with("success", "Staff saved successfully");
        } else {
            return redirect()->route('admin.staffs.index')->with("success", "Staff saved successfully");
        }
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $title = $this->title;
        $breadcrumbs = ['Staff' => route('admin.staffs.index'), 'Edit' => '#'];
        $departments = Department::get();
        $code_no = $customer->staff->code;

        return view('admin.staffs.edit', compact('title', 'customer', 'departments', 'code_no', 'breadcrumbs'));
    }

    public function update(UpdateStaffRequest $request, $id)
    {
        $customer_type = CustomerType::where('name', 'Staff')->first();
        $customer_type_id = $customer_type ? $customer_type->id : null;
        $customer = Customer::where('customer_type_id', $customer_type_id)->findOrFail($id);

        $customer->name = $request->name;
        $customer->phone_no = $request->phone_no;
        $customer->save();
        Staff::updateOrCreate(
            ['customer_id' => $id],
            [
                'department_id' => $request->department_id,
                'code' => $request->code,
            ]
        );
        return redirect()->route('admin.staffs.index')->with("success", "Staff updated successfully");
    }

    public function show($id)
    {
        $title = $this->title;
        $breadcrumbs = ['Staff' => route('admin.staffs.index'), 'Show' => '#'];

        $customer_type = CustomerType::where('name', 'Staff')->first();
        $customer_type_id = $customer_type ? $customer_type->id : null;
        $customer = Customer::where('customer_type_id', $customer_type_id)->findOrFail($id);


        return view('admin.staffs.show', compact('title', 'customer', 'breadcrumbs'));
    }

    public function wallet_transaction($id)
    {

        $customer_type = CustomerType::where('name', 'Staff')->first();
        $customer_type_id = $customer_type ? $customer_type->id : null;
        $customer = Customer::where('customer_type_id', $customer_type_id)->findOrFail($id);
        $transaction_types = TransactionType::whereIn('id', [1, 2])->get();
        $title = $this->title;
        $breadcrumbs = ['Staff' => route('admin.staffs.index'), 'Wallet Transaction' => '#'];
        return view('admin.staffs.wallet_transaction', compact('customer', 'transaction_types', 'title', 'breadcrumbs'));
    }

    public function store_wallet_transaction($id, StoreWalletTransactionRequest $request)
    {
        $customer_type = CustomerType::where('name', 'Staff')->first();
        $customer_type_id = $customer_type ? $customer_type->id : null;
        $customer = Customer::where('customer_type_id', $customer_type_id)->findOrFail($id);
        $transaction_type = TransactionType::whereIn('id', [1, 2])->find($request->transaction_type_id);
        if (!$transaction_type) {
            return redirect()->back()->with('fail', 'Transaction Type Not Found');
        }
        $previous_amount = $customer->wallet_balance();
        if ($transaction_type->is_add) {
            $current_amount = $previous_amount + $request->amount;
        } else {
            $current_amount = $previous_amount - $request->amount;
        }
        CustomerWalletTransaction::create([
            'previous_amount' => $previous_amount,
            'amount' => $request->amount,
            'total_amount' => ($transaction_type->is_add) ? $request->amount : -($request->amount),
            'current_amount' => $current_amount,
            'transaction_type_id' => $transaction_type->id,
            'customer_id' => $customer->id,
            'description' => isset($request->description) ? $request->description : null,
            'author_id' => auth()->id(),
        ]);
        $customer->update([
            'balance' => $current_amount
        ]);

        return redirect()->route('admin.staffs.index')->with("success", "Staff Transaction created successfully");
    }

    public function changeStatus(Request $request)
    {
        $customer_id = $request->customer_id;
        $customer_type = CustomerType::where('name', 'Staff')->first();
        $customer_type_id = $customer_type ? $customer_type->id : null;
        $status =  $request->status;
        $customer = Customer::where('customer_type_id', $customer_type_id)->find($customer_id);
        if ($customer) {
            $customer->update(['status' => $status]);
            return response()->json([
                'success' => true,
                'status' => $status == 1 ? 'Active' : 'Inactive',
                'checked' => $status == 1 ? true : false,
                'message' => 'Status Successfully Changed!',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status' => 'fail',
                'message' => 'Staff Not Found',
            ]);
        }
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $customer_type = CustomerType::where('name', 'Staff')->first();
            $customer_type_id = $customer_type ? $customer_type->id : null;
            $data = Customer::select('table_customers.*')->where('customer_type_id', $customer_type_id)->with('staff.department');
            $canEdit = auth()->user()->can('staff_edit');
            $canShow = auth()->user()->can('staff_list');
            $canWalllettransaction = auth()->user()->can('staff_wallet_transaction');



            return DataTables::of($data)
                ->editColumn('created_at', function (Customer $status) {
                    return [
                        'display' => $status->created_at->diffForHumans(),
                        'timestamp' => $status->created_at
                    ];
                })
                ->editColumn('status', function ($customer) {

                    return ($customer->status) ?
                        '<div class="custom-control custom-switch  ">
                    <input type="checkbox" class="custom-control-input changeStatus" checked data-id="' . $customer->id . '" id="' . $customer->id . '" >
                    <label class="custom-control-label"  for="' . $customer->id . '">Active</label>' :

                        '<div class="form-group ">
                    <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input  changeStatus"  data-id="' . $customer->id . '" id="' . $customer->id . '" >
                    <label class="custom-control-label" for="' . $customer->id . '">Inactive</label>
                   ';
                })
                ->addColumn(
                    'action',
                    function ($row) use ($canEdit, $canShow, $canWalllettransaction) {
                        $walletTransactionBtn = $canWalllettransaction ? '<a href="' . route('admin.staffs.wallet_transaction', $row->id) . '"
                        class="btn btn-xs btn-success" data-toggle="tooltip" title="Wallet Transaction">Wallet Transaction</i></a>' : '';

                        $editBtn = $canEdit ? '<a href="' . route('admin.staffs.edit', $row->id) . '"
                        class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil-alt"></i></a>' : '';
                        $showBtn =   $canShow ? '<a href="' . route('admin.staffs.show', $row->id) . '"
                        class="btn btn-xs btn-primary" data-toggle="tooltip" title="Detail"><i class="fa fa-eye"></i></a>' : '';

                        return $showBtn . ' ' . $editBtn  . ' ' . $walletTransactionBtn;
                    }
                )
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }

    public function getCodeNo()
    {
        $latestStaff = Staff::select('id')->orderBy('created_at', 'desc')->first();

        if ($latestStaff instanceof  Staff) {
            $codeNo = $latestStaff->id + 1;
        } else {
            $codeNo = 1;
        }
        $code_no = str_pad($codeNo, 4, 0, STR_PAD_LEFT);
        return $code_no;
    }
}
