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
                            <table class="table table-bordered ">
                                <thead>
                                    <th>Item Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>SubTotal</th>
                                </thead>
                                <tbody>
                                    @foreach ($order_items as $item)
                                        <tr>
                                            <td>{{ $item->item->name }}</td>
                                            <td>{{ $item->total }}</td>
                                            <td>{{ $item->price }}</td>
                                            <td>{{ $item->sub_total }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="3">Total:</td>
                                        <td>Rs. {{ $order->total }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="row form-inline  d-flex py-2">

                            </div>
                            <div class="row py-2">
                                <div class="col-md-6">
                                    <label for="">Discount</label>
                                    <input name="discount" value="0" max="{{ $order->total }}" class="form-control" type="number"     >
                                </div>
                                <div class="col-md-6">
                                    <label for="">Payment Type:</label>
                                    <select name="payment_type" required class="form-control  float-right" >
                                        <option value="cash">Cash</option>
                                        <option value="bank">Bank</option>
                                    </select>
                                </div>
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
