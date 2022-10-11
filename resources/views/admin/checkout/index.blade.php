@extends('layouts.admin.master')
@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <form action="{{ route('admin.orders.checkout.store', $order->id) }}" id="checkout-form" method="POST">
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
                                @isset($order->customer)
                                    <p>Customer Name:{{ $order->customer->name }}</p>
                                @endisset
                                @isset($wallet_balance)
                                    <p>Wallet Balance: {{ $wallet_balance }}</p>
                                @endisset

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
                                                <td>Rs. {{ $item->price }}</td>
                                                <td width="200px">Rs. {{ $item->sub_total }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                    <tr>
                                        <td colspan="3">Total:</td>
                                        <td>Rs. {{ $order->total }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Discount:</td>
                                        <td class="btn-group"><input id="discount" value="0"
                                                max="{{ $order->total }}" class="form-control form-control-sm "
                                                type="number">
                                            <input type="hidden" id="discount-amount" name="discount" />
                                            <button id="apply-discount" type="button"
                                                class="btn btn-primary btn-sm ml-2">Apply</button>
                                        </td>
                                    </tr>
                                    @if ($service_charge)
                                        <tr>
                                            <td colspan="3">Service Charge:</td>
                                            <td id="service-charge">Rs. {{ $order->serviceCharge() }}</td>
                                        </tr>
                                    @endif
                                    @if ($tax)
                                        <tr>
                                            <td colspan="3">Tax Amount:</td>
                                            <td id="tax-amount">Rs. {{ $order->taxAmount() }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="3">Grand Total:</td>
                                        <td id="grand-total">Rs. {{ $order->totalWithTax() }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Payment Type:</td>

                                        <td> <select name="payment_type_id"
                                                class="form-control form-control-sm  float-right" id="payment_type_id" required>
                                                @foreach ($payment_types as $payment_type)
                                                    <option value="{{ $payment_type->id }}">{{ $payment_type->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Paid Amount:</td>
                                        <td> <input type="number" value="{{ $order->totalWithTax() }}" readonly
                                                 step="0.01" min="0"
                                                class="form-control form-control-sm" name="paid_amount" id="paid_amount"
                                                required></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Due Amount:</td>
                                        <td> <input type="number" value="0" class="form-control form-control-sm"
                                                min="0"  readonly
                                                name="due_amount" id="due_amount" required></td>
                                    </tr>

                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button id="print-bill" class="btn btn-primary">Checkout and Print Bill</button>
                        <button type="submit" class="btn btn-primary">Checkout</button>
                    </div>
                </form>
            </div>
            @include('admin.orders._orderDetailModal')
        </div>
    </div>
@endsection
@section('js')
    <script>
        let total = {!! $order->total !!};
        let tax = {!! $tax !!};
        let service_charge = {!! $service_charge !!};
        let grand_total = {!! $order->totalWithTax() !!}
        //For enabling paid amount
        let payment_type_id = $('#payment_type_id').val();
        if (payment_type_id == 3) {
            $('#paid_amount').attr('readonly', false);
        }
        //For Applying Discount
        $(function() {
            $('#apply-discount').on('click', function(e) {
                let discount = parseFloat($('#discount').val());
                if (isNaN(discount)) {
                    discount = 0;
                }
                if ($('#discount')[0].checkValidity()) {
                    $('#discount-amount').val(discount);
                    calculateSetServiceChargeAndTax(discount);

                } else {

                    $("#discount")[0].reportValidity();
                }

            })
        });
        $(window).keydown(function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                $('#apply-discount').trigger('click');
                return false;
            }
        });
        $('#payment_type_id').change(function() {
            if ($(this).val() != 3) {
                $('#paid_amount').val(grand_total);
                $('#due_amount').val(0);

                $('#paid_amount').attr('readonly', 'readonly');
            } else {
                $('#paid_amount').val(0);
                $('#due_amount').val(grand_total);

                $('#paid_amount').attr('readonly', false);

            }
        });
        $('#paid_amount').keyup(function() {
            let due = (grand_total - parseFloat($(this).val()));
            $('#due_amount').val(due.toFixed(2));

        });
        $('#print-bill').on('click', function(e) {
            e.preventDefault();
            $('#modal-lg').modal('toggle');
            // $('.get-detail').attr('disabled', true);
            clearModal();

            // $(this).attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: '{{ route('admin.orders.checkout.store', $order->id) }}',
                data: $('#checkout-form').serialize(),
                beforeSend: function() {
                    $('#overlay').show();
                },
                success: function(data) {
                    if (data.status === 'success') {
                        setModalData(data.order);

                        $('#get-bill').attr('href', data.billRoute);

                        data.orderItems.forEach(function(item) {
                            $('#table-items').append(templateItem(item.item.name, item
                                .total, item.price));
                        });
                        if (data.order.discount) {
                            $('#table-items').append(
                                "<tr><td colspan='3'>Discount</td><td>" +
                                foramtValue(data.order.discount) + "</td></tr>");
                        }
                        if (data.order.service_charge) {
                            $('#table-items').append(
                                "<tr><td colspan='3'>Service Charge</td><td>" +
                                foramtValue(data.order.service_charge) + "</td></tr>");
                        }
                        if (data.order.tax) {
                            $('#table-items').append(
                                "<tr><td colspan='3'>Tax</td><td>" +
                                foramtValue(data.order.tax) + "</td></tr>");
                        }
                        if (data.order.net_total) {
                            $('#table-items').append("<tr><td colspan='3'>Net Total</td><td>" +
                                foramtValue(data.order.net_total) + "</td></tr>");
                        }


                    } else {
                        console.log('false');
                    }
                    $('#overlay').hide();
                    $('.get-detail').attr('disabled', false);


                },
                error: function(xhr) {
                    $(this).attr('disabled', false);
                    $('#overlay').hide();
                    $('.get-detail').attr('disabled', false);
                    console.log('Internal Serve Error');
                }
            });
        });


        function templateItem(name, total_quantity, price) {

            return '<tr><td>' + name + '</td><td>' + total_quantity + '</td><td>Rs. ' +
                price + '</td><td>Rs. ' +
                price * total_quantity + '</td><</tr>';
        }

        function clearModal() {
            $('#bill-no').html('');
            $('#customer-name').html('');
            $('#customer-contact').html('');
            $('#order-date').html('');

            $('#order-status').html('');
            $('#table-items').html('');
            $('#get-bill').attr('href', 'javascript:void(0)');
            $('#paymentType').css('display', 'none');
            $('#payment_type').html('');
            $('#order-status').html('');

        }

        function setModalData(order) {
            $('#bill-no').html(order.bill_no);
            $('#customer-name').html(order.customer.name);
            $('#customer-contact').html(order.customer.phone_no);
            $('#order-date').html(order.order_datetime);
            if (order.payment_type_id) {
                $('#paymentType').css('display', 'block');
                $('#payment-type').html(order.payment_type.name);
            }
            $('#order-status').html(order.status.title);
        }

        function calculateSetServiceChargeAndTax(discount) {

            let net_total = parseFloat(total) - discount;
            if (net_total >= 0) {
                let service_charge_amount = parseFloat((parseFloat((service_charge / 100) * net_total)).toFixed(2));
                let tax_amount = parseFloat(((parseFloat(net_total) + parseFloat(service_charge_amount)) * (tax / 100))
                    .toFixed(2));

                grand_total = ((net_total + service_charge_amount) + tax_amount).toFixed(2);

                $('#service-charge').text(foramtValue(service_charge_amount));
                $('#tax-amount').text(foramtValue(tax_amount));
                $('#grand-total').text(foramtValue(grand_total));
                $('#paid_amount').val(grand_total);
                $('#due_amount').val(0);


            } else {
                alert('Discount cannot be greater than total')
            }
        }

        function foramtValue(val) {
            return 'Rs. ' + val;
        }
    </script>
@endsection
