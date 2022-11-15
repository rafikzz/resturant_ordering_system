<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCouponRequest;
use App\Http\Requests\Admin\UpdateCouponRequest;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:coupon_list|coupon_create|coupon_edit|coupon_delete', ['only' => ['index','show','getData']]);
        $this->middleware('permission:coupon_create', ['only' => ['create','store']]);
        $this->middleware('permission:coupon_edit', ['only' => ['edit','update',]]);
        $this->middleware('permission:coupon_delete', ['only' => ['destroy']]);
        $this->title = 'Coupon Management';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = $this->title;
        $coupons =Coupon::latest()->get();
        $breadcrumbs =[ 'Coupon'=>'#'];
        return view('admin.coupons.index', compact('title','breadcrumbs','coupons'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = $this->title;
        $breadcrumbs =[ 'Coupon'=>route('admin.coupons.index'), 'Create'=>'#'];


        return view('admin.coupons.create', compact('title','breadcrumbs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCouponRequest $request)
    {
        Coupon::create([
            'title' => $request->title,
            'discount' => $request->discount,
            'expiry_date' => Carbon::parse($request->expiry_date)->format('Y-m-d'),
        ]);

        if(isset($request->new))
        {
            return redirect()->route('admin.coupons.create')->with("success", "Coupon saved successfully");

        }else{
            return redirect()->route('admin.coupons.index')->with("success", "Coupon saved successfully");

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Coupon $coupon)
    {
        $title = $this->title;
        $breadcrumbs =[ 'Coupon'=>route('admin.coupons.index'), 'Edit'=>'#'];


        return view('admin.coupons.edit', compact('title','breadcrumbs','coupon'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        $coupon->title =$request->title;
        $coupon->discount =$request->discount;
        $coupon->expiry_date =Carbon::parse($request->expiry_date)->format('Y-m-d');


        $coupon->save();
        return redirect()->route('admin.coupons.index')->with("success", "Coupon updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with("success", "Coupon deleted successfully");

    }
}
