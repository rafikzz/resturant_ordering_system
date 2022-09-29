@extends('layouts.admin.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Order Breakdown</h3>
                </div>
                <form action="{{ route('admin.orders.breakdown.store', $order->id) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <b>Bill No</b>: <b id="bill-no">{{ $order->bill_no }}</b>
                            </div>
                            <div class="col-12">
                                <b>Table No</b>: <b id="customer-name">{{ $order->table_no }}</b>
                            </div>
                            <div class="col-12">
                                <b>Customer Name</b>: <b id="customer-name">{{ $order->customer->name }}</b>
                            </div>

                            <div class="col-12">
                                <b>Order Date</b>: <b id="order-date">{{ $order->order_date }}</b>
                            </div>
                            <hr>
                            <div class="col-6">
                                <label for="">Transfer Order to Customer</label>
                                <select name="customer_id" class="form-control form-control-sm select2" required>
                                    <option value="">--Please Select Customer--</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">
                                            {{ $customer->phone_no }} ({{ $customer->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <h3>Order List</h3>
                            <table class="table  table-sm">
                                <thead>
                                    <th>Item</th>
                                    <th>Max Quantity</th>
                                    <th>Transfer Quantity</th>
                                </thead>
                                @foreach ($orderItems as $item)
                                    <tr>
                                        <td>{{ $item->item->name }} </td>
                                        <td>{{ $item->total_quantity }}</td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm " min="0"
                                            name="qunatity[{{ $item->item_id }}]"   max="{{ $item->total_quantity }}" value="0" required>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="text-center">
                            <button class="btn btn-primary btn-sm" name="new" type="submit">Save and Continue</button>
                            <button class="btn btn-primary btn-sm" type="submit">Save and Exit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
@endsection
