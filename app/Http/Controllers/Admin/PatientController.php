<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OrdersByCustomerExport;
use App\Exports\PatientOrderItems;
use App\Exports\PatientOrdersExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePatitentRequest;
use App\Http\Requests\Admin\StoreStaffRequest;
use App\Http\Requests\Admin\UpdatePatitentRequest;
use App\Http\Requests\Admin\UpdateStaffRequest;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\CustomerWalletTransaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Patient;
use App\Models\PatientDischargePaymentRecord;
use App\Models\TransactionType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class PatientController extends Controller
{
    private $title = null;

    public function __construct()
    {
        $this->middleware('permission:patient_list|patient_create|patient_edit|patient_delete', ['only' => ['index', 'show', 'getData']]);
        $this->middleware('permission:patient_create', ['only' => ['create', 'store']]);
        $this->middleware('permission:patient_edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:patient_discharge', ['only' => ['discharge']]);

        $this->title = 'Patient Management';
    }
    public function index(Request $request)
    {
        $title = $this->title;
        $breadcrumbs = ['Patient' => route('admin.patients.index')];
        return view('admin.patients.index', compact('title', 'breadcrumbs'));
    }

    public function create()
    {
        $title = $this->title;
        $breadcrumbs = ['Patient' => route('admin.patients.index'), 'Create' => '#'];

        return view('admin.patients.create', compact('title', 'breadcrumbs'));
    }

    public function store(StoreStaffRequest $request)
    {
        $customer_type = CustomerType::where('name', 'Patient')->first();
        $customer_type_id = $customer_type ? $customer_type->id : null;

        DB::beginTransaction();
        try {
            $customer = Customer::create([
                'name' => $request->name,
                'customer_type_id' => $customer_type_id,
                'phone_no' => $request->phone_no,
                'is_staff' => 0,
                'status' => 1,
            ]);
            Patient::create([
                'customer_id' => $customer->id,
                'register_no' => $request->register_no,
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
        DB::commit();



        if (isset($request->new)) {
            return redirect()->route('admin.patients.create')->with("success", "Patient saved successfully");
        } else {
            return redirect()->route('admin.patients.index')->with("success", "Patient saved successfully");
        }
    }

    public function edit($id)
    {
        $customer_type = CustomerType::where('name', 'Patient')->first();
        $customer_type_id = $customer_type ? $customer_type->id : null;
        $customer = Customer::where('customer_type_id', $customer_type_id)->findOrFail($id);

        $title = $this->title;
        $breadcrumbs = ['Patient' => route('admin.patients.index'), 'Edit' => '#'];

        return view('admin.patients.edit', compact('title', 'customer', 'breadcrumbs'));
    }

    public function update(UpdateStaffRequest $request, $id)
    {
        $customer_type = CustomerType::where('name', 'Patient')->first();
        $customer_type_id = $customer_type ? $customer_type->id : null;
        $customer = Customer::where('customer_type_id', $customer_type_id)->findOrFail($id);

        $customer->name = $request->name;
        $customer->phone_no = $request->phone_no;
        $customer->save();
        Patient::updateOrCreate(
            ['customer_id' => $id],
            [
                'register_no' => $request->register_no
            ]
        );
        return redirect()->route('admin.patients.index')->with("success", "Patient updated successfully");
    }

    public function show($id)
    {
        $customer_type = CustomerType::where('name', 'Patient')->first();
        $customer_type_id = $customer_type ? $customer_type->id : null;
        $customer = Customer::where('customer_type_id', $customer_type_id)->findOrFail($id);
        $customer_orders_total = Order::where('customer_id', $id)->sum('total');
        $customer_orders_count = Order::where('customer_id', $id)->count();


        $title = $this->title;
        $breadcrumbs = ['Patient' => route('admin.patients.index'), 'Show' => '#'];


        return view('admin.patients.show', compact('title', 'customer', 'breadcrumbs', 'customer_orders_count', 'customer_orders_total'));
    }

    public function getData(Request $request)
    {
        $customer_type = CustomerType::where('name', 'Patient')->first();
        $customer_type_id = $customer_type ? $customer_type->id : null;
        $data = Customer::select('table_customers.*')->withCount(['orders as orders_total'=>function($query) {
            $query->select(DB::raw('sum(net_total)'));
        }])->with('patient')->where('customer_type_id', $customer_type_id)->where('status', 1)->get();

        if ($request->ajax()) {
            if ($request->mode) {
                $data = Customer::select('table_customers.*')->withCount(['orders as orders_total'=>function($query) {
                    $query->select(DB::raw('sum(net_total)'));
                }])->with('patient')->where('customer_type_id', $customer_type_id)->where('status', 1);
            } else {
                $data = Customer::select('table_customers.*')->withCount(['orders as orders_total'=>function($query) {
                    $query->select(DB::raw('sum(net_total)'));
                }])->with('patient')->where('customer_type_id', $customer_type_id)->where('status', 0);
            }
            $canEdit = auth()->user()->can('patient_edit');
            $canDischarge = auth()->user()->can('patient_discharge');



            return DataTables::of($data)
                ->editColumn('created_at', function (Customer $status) {
                    return [
                        'display' => Carbon::parse($status->created_at)->format('Y-m-d g:i a '),
                        'timestamp' => $status->created_at
                    ];
                })

                ->addColumn(
                    'action',
                    function ($row) use ($canEdit, $canDischarge) {
                        $editBtn = $canEdit ? '<a href="' . route('admin.patients.edit', $row->id) . '"
                        class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil-alt"></i></a>' : '';
                        if ($row->status) {
                            $dischargeBtn = $canDischarge ? '<a href="' . route('admin.patients.discharge', $row->id) . '"
                            class="btn btn-xs btn-success" data-toggle="tooltip" title="Discharge"><i class="fa fa-directions"> Discharge</i></a>' : '';
                        } else {
                            $dischargeBtn = null;
                        }


                        $showBtn = '<a href="' . route('admin.patients.show', $row->id) . '"
                        class="btn btn-xs btn-primary" data-toggle="tooltip" title="Show"><i class="fa fa-eye"></i></a>';
                        $exportBtn = '<a href="' . route('admin.patients.export', $row->id) . '"
                        class="btn btn-xs btn-success">Export Order</i></a>';
                        $btn = $showBtn . ' ' . $editBtn . ' ' . $dischargeBtn.' '.$exportBtn;

                        return $btn;
                    }
                )
                ->make(true);
        }
    }
    public function getOrderItemData(Request $request)
    {

        if ($request->ajax()) {
            $customerId = $request->customer_id;

            $data = OrderItem::select('table_order_items.*')->with('item')->with('order:id,bill_no')->selectRaw('table_order_items.id,table_order_items.price, table_order_items.total,(table_order_items.total * table_order_items.price) as total_price')->whereHas('order', function ($q) use ($customerId) {
                $q->where('customer_id', $customerId)->where('status_id', 3);
            });

            return DataTables::of($data)
                ->make(true);
        }
    }

    public function discharge_show($id)
    {
        $customer_type = CustomerType::where('name', 'Patient')->first();
        $customer_type_id = $customer_type ? $customer_type->id : null;
        $customer_orders_total = Order::where('customer_id', $id)->sum('net_total');
        $customer_orders_count = Order::where('customer_id', $id)->count();
        $customer = Customer::where('customer_type_id', $customer_type_id)->where('status', 1)->findOrFail($id);



        return view('admin.patients.discharge', compact('customer', 'customer_orders_total'));
    }

    public function discharge(Request $request, $id)
    {

        DB::beginTransaction();
        try {
            $customer_type = CustomerType::where('name', 'Patient')->first();
            $customer_type_id = $customer_type ? $customer_type->id : null;
            $customer = Customer::where('customer_type_id', $customer_type_id)->where('status', 1)->findOrFail($id);
            PatientDischargePaymentRecord::create([
                'total_amount' => $request->order_total,
                'customer_id' => $id,
                'paid_amount' => $request->paid_amount,
                'discount' => $request->discount_amount,

            ]);
            if ($customer instanceof Customer) {


                $customer->update([
                    'balance' => 0,
                    'status' => 0,
                ]);
            } else {
                return redirect()->route('admin.patients.index')->with('failed', 'Patient already Discharged');
            }
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
        DB::commit();


        return redirect()->route('admin.patients.index')->with('success', 'Patient Discharged Successfully');
    }

    public function export($id)
    {

        // $customers =Customer::with('orders')->with('patient')->whereHas('patient')->get();
        // return view('admin.excel_export.customer_order_items',compact('customers'));
        $customer_type = CustomerType::where('name', 'Patient')->first();
        $customer_type_id = $customer_type ? $customer_type->id : null;
        $customer = Customer::where('customer_type_id', $customer_type_id)->findOrFail($id);
        // dd( Customer::where('customer_type_id',3)->whereHas('orders')->where('status',1)->get());

        $excelname= $customer->name.'_'.$customer->patient->register_no.'.xlsx';
        return Excel::download(new PatientOrderItems($id), $excelname);
    }

    public function exportOrderItems()
    {
        $excelname= 'Patient_'.Carbon::now().'.xlsx';
        return Excel::download(new OrdersByCustomerExport(), $excelname);

    }

}
