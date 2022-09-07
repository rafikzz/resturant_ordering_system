<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
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
        $data = Customer::select('*')->orderBy('id', 'desc')->get();

        return view('admin.customers.index', compact('title'));
    }
    public function show(Customer $customer)
    {
        $title = $this->title;

       return view('admin.customers.show',compact('title','customer'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Customer::select('*');

            return DataTables::of($data)
                ->editColumn('created_at', function (Customer $status) {
                    return [
                        'display' => $status->created_at->diffForHumans(),
                        'timestamp' => $status->created_at
                    ];
                })
                ->addColumn(
                    'action',
                    function ($row) {
                        return '<a href="'.route('admin.customers.show', $row->id).'"
                        class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>';
                    }
                )
                ->make(true);
        }
    }
}
