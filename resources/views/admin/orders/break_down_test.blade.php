@extends('layouts.admin.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Order Breakdown</h3>
                </div>
                <form action="{{ route('admin.orders.breakdown.store.test', $order->id) }}" method="POST" id="breakdown-form">
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
                        </div>
                        <div class="row mt-3">
                            <h3>Order List</h3>
                            <table class="table  table-sm">
                                <thead>
                                    <th>Customer</th>
                                    <th>Item</th>
                                    <th>Max Quantity</th>
                                    <th>Transfer Quantity</th>
                                    <th>Action</th>
                                </thead>
                                <tbody id="orderItems">
                                    <tr>
                                        <td width="30%">
                                            <select name="customer_id[0]" id="customer-0"
                                                class="form-control form-control-sm select2" required>
                                                <option value="">--Please Select Customer--</option>
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}">
                                                        {{ $customer->phone_no }} ({{ $customer->name }})</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td width="20%">
                                            <select name="item_id[0]" id="item-0"
                                                class="form-control form-control-sm select2  item-select " required>
                                                <option value="">Select Item</option>
                                                @foreach ($itemDictionary as $key => $item)
                                                    <option value="{{ $key }}">{{ $item }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="max_quantity">0</td>
                                        <td width="30%">
                                            <input type="number" id="quantity-0"
                                                class="form-control form-control-sm transfer_quantity" min="1"
                                                name="quantity[0]" value="0" required>
                                        </td>
                                        <td>
                                            <button id="add-more" type="button" class="btn btn-success btn-sm"><i
                                                    class="fa fa-plus"></i></button>
                                        </td>
                                    </tr>
                                </tbody>

                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="text-center">
                            <button class="btn btn-primary btn-sm" name="new" value="1" type="submit">Save and
                                Continue</button>
                            <button class="btn btn-primary btn-sm" type="submit">Save and Exit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        var itemQuantityDictionary = {!! $itemQuantityDictionary !!};
        var itemDictionary = {!! $itemDictionary !!};
        var customers = {!! $customers !!};

        var i = 0;
        $('#breakdown-form').validate({
            errorPlacement: function(error, element) {
                error.appendTo(element.parent("td"));
            },
            submitHandler: function(form) {
                // do other things for a valid form
                $('#orderItems  .select2').attr({
                    'disabled': false
                });
                form.submit();
            }
        });
        $(document).on('change', '.item-select', function() {
            let item = $(this);
            let item_id = $(this).val();
            console.log(item_id, itemQuantityDictionary[item_id]);
            if (item_id) {
                item.parent('td').closest('tr').find('.max_quantity').html(itemQuantityDictionary[item_id]);
                item.parent('td').closest('tr').find('.transfer_quantity').attr('max', itemQuantityDictionary[
                    item_id]);

            } else {
                item.parent('td').closest('tr').find('.max_quantity').find('.max_quantity').html(0);
            }
        });

        $('#add-more').click(function() {
            if ($("#breakdown-form").valid()) {
                i++;
                let item_quantity = parseInt($("#orderItems tr:last").find('.transfer_quantity').val());
                let item_id = parseInt($("#orderItems tr:last").find('.item-select').val());
                itemQuantityDictionary[item_id] -= item_quantity;

                $("#orderItems tr:last").find('input').attr('readonly', 'readonly');
                $("#orderItems tr:last").find('input').attr('readonly', 'readonly');
                $('#orderItems tr:last .select2').attr({
                    'disabled': 'readonly'
                });
                $('#orderItems').append(template());
                customers.forEach(customer => {
                    $('#customer-' + i).append($('<option>', {
                        value: customer.id,
                        text: customer.phone_no + '(' + customer.name + ')',
                    }));
                });
                $('#customer-' + i).select2();

                for (let key in itemQuantityDictionary) {
                    if (itemQuantityDictionary[key] > 0) {
                        $('#item-' + i).append($('<option>', {
                            value: key,
                            text: itemDictionary[key],
                        }));
                    }

                }

            }

        });
        $(document).on('click', '.tr-remove', function() {
            let tr = $(this).parent('td').closest('tr');
            $(this).parent('td').closest('tr').remove();

            let item_quantity = parseInt($("#orderItems tr:last").find('.transfer_quantity').val());
            let item_id = parseInt($("#orderItems tr:last").find('.item-select').val());
            itemQuantityDictionary[item_id] = parseInt(item_quantity) + parseInt(itemQuantityDictionary[item_id]);

            $("#orderItems tr:last").find('input').attr('readonly', false);
            $('#orderItems tr:last .select2').attr({
                'disabled': false
            });
        });

        function template() {
            let template = '<tr><td width="30%"> <select name="customer_id[' +
                i + ']" id="customer-' + i +
                '" class="form-control form-control-sm select2" required><option value="">--Please Select Customer--</option> </select> </td> <td width="20%"> <select name="item_id[' +
                i +
                ']" id="item-' + i +
                '" class="form-control form-control-sm  item-select select2" required> <option value="">Select Item</option> </select> </td> <td class="max_quantity">0</td> <td width="30%"> <input type="number" id="quantity-' +
                i + '" class="form-control form-control-sm transfer_quantity" min="1" name="quantity[' +
                i +
                ']" value="0" required> </td> <td> <button  type="button" class="btn tr-remove btn-danger btn-sm"><i class="fa fa-minus"></i></button></td></tr>';
            return template;
        }
    </script>
@endsection
