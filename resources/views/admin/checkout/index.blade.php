@extends('layouts.admin.master')
@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <form action="{{ route('admin.orders.checkout.store',$order->id) }}" method="POST">
                    @csrf
                    <div class="card-header">
                        <h5 class="card-title">Checkout</h5>
                        <div class="card-tools">
                            <a class="btn btn-primary" href="{{ route('admin.items.index') }}"> Back</a></i></a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div>
                            <div>
                                <p>Bill No:{{ $order->bill_no }}</p>
                                <p>Customer Name:{{ $order->customer->name }}</p>

                            </div>
                            <div class="row">
                                <h3>Order List</h3>
                                <table class="table table-hover table-sm" style="  min-height: 20vh; ">
                                    @foreach ($order_items as $order_no => $items)
                                        <thead>
                                            <th>Order Slip {{ $order_no }}</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>SubTotal</th>
                                        </thead>
                                        @foreach ($items as $item)

                                            <tr>
                                                <td>{{ $item->item->name }}</td>
                                                <td>{{ $item->total }}</td>
                                                <td>{{ $item->price }}</td>
                                                <td width="200px">{{ $item->sub_total }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                    <tr>
                                        <td colspan="3">Total:</td>
                                        <td>Rs. {{ $order->total }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Discount:</td>
                                        <td><input name="discount" value="0" max="{{ $order->total }}" class="form-control form-control-sm " type="number"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Service Charge:</td>

                                        <td class="service-charge">0.00</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Tax Amount:</td>
                                        <td class="tax-amount">0.00</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Payment Type:</td>

                                        <td class="service-charge">  <select name="payment_type" required class="form-control form-control-sm  float-right" >
                                            <option value="cash">Cash</option>
                                            <option value="bank">Bank</option>
                                        </select></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Total:</td>
                                        <td>Rs. {{ $order->total }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button class="btn btn-primary">Checkout</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
