<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentType;
use Illuminate\Http\Request;

class PaymentTypeController extends Controller
{
    public function __construct() {
        $this->middleware('permission:payment_type_list', ['only' => ['index', 'changeStatus']]);
        $this->middleware('permission:payment_type_edit', ['only' => ['index', 'changeStatus']]);
        $this->title = 'Payment Type Management';
    }

    public function index()
    {
        $payment_types =PaymentType::get();
        $breadcrumbs = ['Payment Type' => '#'];
        $title =$this->title;
        return view('admin.payment_types.index',compact('payment_types','breadcrumbs','title'));
    }

    public function changeStatus(Request $request)
    {
        $payment_type_id = $request->payment_type_id;
        $status =  $request->status;
        PaymentType::whereId($payment_type_id)->update(['status' => $status]);

        return response()->json([
            'success' => true,
            'status' => $status == 1 ? 'Active' : 'Inactive',
            'checked' => $status == 1 ? true : false,
            'message' => 'Status Successfully Changed!',
        ]);
    }
}
