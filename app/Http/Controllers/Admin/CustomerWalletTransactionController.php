<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreWalletTransactionRequest;
use App\Models\Customer;
use App\Models\CustomerWalletTransaction;
use App\Models\TransactionType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CustomerWalletTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:customer_list|customer_create|customer_edit|customer_delete', ['only' => ['index','show','getData']]);
        $this->middleware('permission:customer_create', ['only' => ['create','store']]);
        $this->title = 'Customer Wallet Transaction History';
    }
    public function index($id)
    {
        $customer =Customer::findOrFail($id);
        $title = $this->title;
        $breadcrumbs =[ 'Customer'=>route('admin.customers.index'), 'Wallet Transaction'=>'#'];
        return view('admin.customer_wallet_transactions.index',compact('customer','title','breadcrumbs'));
    }

    public function create($id)
    {
        $customer =Customer::findOrFail($id);
        $title = $this->title;
        $transaction_types = TransactionType::get()->except(3);
        $breadcrumbs =[ 'Customer'=>route('admin.customers.index'), 'Wallet Transaction'=>route('admin.customers.wallet_transactions.index',$customer->id),
                        'Create'=>'#'];
        return view('admin.customer_wallet_transactions.create',compact('customer','title','breadcrumbs','transaction_types'));

    }

    public function store($id,StoreWalletTransactionRequest $request)
    {
        $customer =Customer::findOrFail($id);
        $transaction_type = TransactionType::find($request->transaction_type_id);
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

        return redirect()->route('admin.customers.wallet_transactions.index',$customer->id)->with("success", "Customer saved successfully");

    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {

            $data = CustomerWalletTransaction::select('table_customer_wallet_transactions.*')->where('table_customer_wallet_transactions.customer_id',$request->customer_id)->with('transaction_type:id,name,is_add')->with('order:id,bill_no,net_total')->with('author:id,name');
            $canEdit = auth()->user()->can('customer_edit');
            return DataTables::of($data)
                ->editColumn('created_at', function ($row) {
                    return [
                        'display' => $row->created_at->diffForHumans(),
                        'timestamp' => $row->created_at
                    ];
                })
                ->addColumn(
                    'action',
                    function ($row)use ($canEdit) {
                        return 'No Action';
                    }
                )
                ->make(true);
        }
    }
}
