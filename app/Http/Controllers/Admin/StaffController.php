<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCustomerRequest;
use App\Http\Requests\Admin\StoreWalletTransactionRequest;
use App\Http\Requests\Admin\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\CustomerWalletTransaction;
use App\Models\TransactionType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StaffController extends Controller
{
    private $title = null;

    public function __construct()
    {
        $this->middleware('permission:staff_list|staff_create|staff_edit|staff_delete', ['only' => ['index','show','getData']]);
        $this->middleware('permission:staff_create', ['only' => ['create','store']]);
        $this->middleware('permission:staff_wallet_transaction', ['only' => ['wallet_transaction','store_wallet_transaction']]);

        $this->title = 'Staff Management';
    }
    public function index(Request $request)
    {
        $title = $this->title;
        $breadcrumbs =[ 'Staff'=>route('admin.staffs.index')];
        return view('admin.staffs.index', compact('title','breadcrumbs'));
    }

    public function create()
    {
        $title = $this->title;
        $breadcrumbs =[ 'Staff'=>route('admin.staffs.index'),'Create'=>'#'];

        return view('admin.staffs.create', compact('title','breadcrumbs'));

    }

    public function store(StoreCustomerRequest $request)
    {

        Customer::create([
            'name' => $request->name,
            'phone_no'=> $request->phone_no,
            'is_staff'=> 1,
            'status'=>1,
        ]);
        if(isset($request->new))
        {
            return redirect()->route('admin.staffs.create')->with("success", "Customer saved successfully");

        }else{
            return redirect()->route('admin.staffs.index')->with("success", "Customer saved successfully");

        }
    }

    public function edit($id)
    {
        $customer=Customer::findOrFail($id);
        $title = $this->title;
        $breadcrumbs =[ 'Staff'=>route('admin.staffs.index'),'Edit'=>'#'];

        return view('admin.staffs.edit', compact('title','customer','breadcrumbs'));

    }

    public function update(UpdateCustomerRequest $request, $id)
    {
        $customer =Customer::findOrFail($id);

        $customer->name =$request->name;
        $customer->phone_no =$request->phone_no;
        $customer->save();
        return redirect()->route('admin.staffs.index')->with("success", "Customer updated successfully");
    }

    public function show($id)
    {
        $title = $this->title;
        $breadcrumbs =[ 'Staff'=>route('admin.staffs.index'), 'Show'=>'#'];

        $customer =Customer::where('is_staff',1)->findOrFail($id);


       return view('admin.staffs.show',compact('title','customer','breadcrumbs'));
    }

    public function wallet_transaction($id)
    {
        $customer =Customer::findOrFail($id);
        $transaction_types =TransactionType::whereIn('id',[1,2])->get();
        $title = $this->title;
        $breadcrumbs =[ 'Staff'=>route('admin.staffs.index'), 'Wallet Transaction'=>'#'];
        return view('admin.staffs.wallet_transaction',compact('customer','transaction_types','title','breadcrumbs'));
    }

    public function store_wallet_transaction($id,StoreWalletTransactionRequest $request)
    {
        $customer =Customer::where('is_staff',1)->findOrFail($id);
        $transaction_type = TransactionType::whereIn('id',[1,2])->find($request->transaction_type_id);
        if(!$transaction_type)
        {
            return redirect()->back()->with('fail','Transaction Type Not Found');
        }
        $previous_amount= $customer->wallet_balance();
        if($transaction_type->is_add)
        {
            $current_amount= $previous_amount + $request->amount;
        }else
        {
            $current_amount= $previous_amount - $request->amount;

        }
        CustomerWalletTransaction::create([
            'previous_amount'=>$previous_amount,
            'amount'=>$request->amount,
            'total_amount'=> $request->amount,
            'current_amount'=> $current_amount,
            'transaction_type_id'=>$transaction_type->id,
            'customer_id'=>$customer->id,
            'description'=>isset($request->description)?$request->description:null,
            'author_id'=>auth()->id(),
        ]);
        $customer->update([
            'balance'=> $current_amount
        ]);

        return redirect()->route('admin.staffs.index',$customer->id)->with("success", "Staff Transaction created successfully");

    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Customer::select('table_customers.*')->where('is_staff',1);
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

                ->addColumn(

                    'action',
                    function ($row)use ($canEdit,$canShow,$canWalllettransaction) {
                        $walletTransactionBtn = $canWalllettransaction? '<a href="'.route('admin.staffs.wallet_transaction', $row->id).'"
                        class="btn btn-xs btn-success">Wallet Transaction</i></a>':'';

                        $editBtn = $canEdit? '<a href="'.route('admin.staffs.edit', $row->id).'"
                        class="btn btn-xs btn-warning"><i class="fa fa-pencil-alt"></i></a>':'';
                        $showBtn =   $canShow?'<a href="'.route('admin.staffs.show', $row->id).'"
                        class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a>':'';

                        return $showBtn.' '.$editBtn  .' '. $walletTransactionBtn ;
                    }
                )
                ->make(true);
        }
    }
}
