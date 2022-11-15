<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCustomerRequest;
use App\Http\Requests\Admin\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    private $title = null;

    public function __construct()
    {
        $this->middleware('permission:customer_list|customer_create|customer_edit|customer_delete', ['only' => ['index','show','getData']]);
        $this->middleware('permission:customer_create', ['only' => ['create','store']]);
        $this->title = 'Customer Management';
    }
    public function index(Request $request)
    {
        $title = $this->title;
        $breadcrumbs =[ 'Customer'=>route('admin.customers.index')];
        return view('admin.customers.index', compact('title','breadcrumbs'));
    }

    public function create()
    {
        $title = $this->title;
        $breadcrumbs =[ 'Customer'=>route('admin.customers.index'),'Create'=>'#'];

        return view('admin.customers.create', compact('title','breadcrumbs'));

    }

    public function store(StoreCustomerRequest $request)
    {

        Customer::create([
            'name' => $request->name,
            'phone_no'=> $request->phone_no,
        ]);
        if(isset($request->new))
        {
            return redirect()->route('admin.customers.create')->with("success", "Customer saved successfully");

        }else{
            return redirect()->route('admin.customers.index')->with("success", "Customer saved successfully");

        }
    }

    public function edit(Customer $customer)
    {
        $title = $this->title;
        $breadcrumbs =[ 'Customer'=>route('admin.customers.index'),'Edit'=>'#'];

        return view('admin.customers.edit', compact('title','customer','breadcrumbs'));

    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->name =$request->name;
        $customer->phone_no =$request->phone_no;
        $customer->save();
        return redirect()->route('admin.customers.index')->with("success", "Customer updated successfully");
    }

    public function show(Customer $customer)
    {
        $title = $this->title;
        $breadcrumbs =[ 'Customer'=>route('admin.customers.index'), 'Show'=>'#'];


       return view('admin.customers.show',compact('title','customer','breadcrumbs'));
    }

    public function wallet_transaction($id)
    {
        $customer =Customer::findOrFail($id);
        $title = $this->title;
        $breadcrumbs =[ 'Customer'=>route('admin.customers.index'), 'Wallet Transaction'=>'#'];
        return view('admin.customers.wallet_transaction',compact('customer','title','breadcrumbs'));
    }
    public function getType(Request $request)
    {
        if(true)
        {
            $customers =Customer::select('id','name','phone_no')->where('is_staff',$request->customer_type)->where('status',1)->orderBy('name')->get();

            return response()->json([
                'status'=>'success',
                'message'=>'Customer data fetched successfully',
                'customers'=>$customers
            ]);
        }

    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Customer::select('table_customers.*')->with('last_transaction');
            // $da= DB::select("Select table_customers.*,table_customer_wallet_transactions.current_amount from table_customers LEFT JOIN table_customer_wallet_transactions
            // ON table_customers.id = table_customer_wallet_transactions.customer_id and table_customer_wallet_transactions.id IN (select MAX(a2.id) from table_customer_wallet_transactions as a2 join table_customers as u2 on u2.id = a2.customer_id group by u2.id)");
            $canEdit = auth()->user()->can('customer_edit');
            // return DataTables::of($da)->addColumn(
            //             'action',
            //             function ($row)use ($canEdit) {
            //                 $editBtn = $canEdit? '<a href="'.route('admin.customers.edit', $row->id).'"
            //                 class="btn btn-xs btn-warning"><i class="fa fa-pencil-alt"></i></a>':'';
            //                 $showBtn ='<a href="'.route('admin.customers.show', $row->id).'"
            //                 class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a>';
            //                 $walletTransactionBtn = '<a href="'.route('admin.customers.wallet_transactions.index',$row->id).'"
            //                 class="btn btn-xs btn-primary">Wallet History</a>';
            //                 return $showBtn.' '.$walletTransactionBtn.' '.$editBtn;
            //             })->make(true);
            return DataTables::of($data)
                ->editColumn('created_at', function (Customer $status) {
                    return [
                        'display' => $status->created_at->diffForHumans(),
                        'timestamp' => $status->created_at
                    ];
                })

                ->addColumn(
                    'action',
                    function ($row)use ($canEdit) {
                        $editBtn = $canEdit? '<a href="'.route('admin.customers.edit', $row->id).'"
                        class="btn btn-xs btn-warning"><i class="fa fa-pencil-alt"></i></a>':'';
                        $showBtn ='<a href="'.route('admin.customers.show', $row->id).'"
                        class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a>';
                        $walletTransactionBtn = '<a href="'.route('admin.customers.wallet_transactions.index',$row->id).'"
                        class="btn btn-xs btn-primary">Wallet History</a>';
                        return $showBtn.' '.$walletTransactionBtn.' '.$editBtn;
                    }
                )
                ->make(true);
        }
    }
}
