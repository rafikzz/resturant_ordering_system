<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerStatementController extends Controller
{
    private $title = null;

    public function __construct()
    {
        $this->middleware('permission:customer_statetment_list', ['only' => ['index', 'getReport',]]);
        $this->title = 'Customer Statements';
    }

    public function index()
    {
        $customers =Customer::orderBy('name')->get();
        $title = $this->title;
        $breadcrumbs = ['Customer Statement' => '#'];

        return view('admin.reports.customer_statement.index',compact('customers','title','breadcrumbs'));

    }

    public function getReport(Request $request)
    {
        $customerId=$request->customer_id;
    }
}
