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
                                    <p>Customer Type:{{ $order->customer->customer_type->name }}</p>
                                @endisset
                                @if ($order->customer->customer_type_id == 2)
                                    <p>Wallet Balance: {{ $order->customer->balance }}</p>
                                @endif

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
                                        <td colspan="3">Coupon</td>
                                        <td> <select name="coupon_id" class="form-control form-control-sm  float-right"
                                                id="coupon_id">
                                                <option value="">None</option>
                                                @foreach ($coupons as $coupon)
                                                    <option value="{{ $coupon->id }}" rel="{{ $coupon->discount }}">
                                                        {{ $coupon->title }} :Rs
                                                        {{ $coupon->discount }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Discount:</td>
                                        <td class="btn-group"><input id="discount" value="0" min="0"
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
                                        <td colspan="3">Packaging</td>
                                        <td><select name="is_delivery" id="is_delivery"
                                                class="form-control form-control-sm  float-right">
                                                <option value="0" {{ $order->is_delivery == 0 ? 'selected' : '' }}>No
                                                </option>
                                                <option value="1" {{ $order->is_delivery == 1 ? 'selected' : '' }}>Yes
                                                </option>
                                            </select>
                                            @error('is_delivery')
                                                <span class=" text-danger" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </td>

                                    </tr>
                                    <tr id="delivery-charge" style="{{ $order->is_delivery ? '' : 'display:none' }};">
                                        <td colspan="3">Packaging Charge Amount:</td>
                                        <td class="btn-group"><input id="delivery" min="0" step=".01"
                                                class="form-control form-control-sm " value="{{ $delivery_charge }}"
                                                type="number">
                                            <input type="hidden" id="delivery_charge" value="{!! $delivery_charge !!}"
                                                name="delivery_charge">
                                            <button id="apply-charge" type="button"
                                                class="btn btn-primary btn-sm ml-2">Apply</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Grand Total:</td>
                                        <td id="grand-total">Rs. {{ $order->totalWithTax() }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Payment Type:</td>

                                        <td> <select name="payment_type" class="form-control form-control-sm  float-right"
                                                id="payment_type" required="">
                                                @if ($order->customer->customer_type_id == 2)
                                                    <option value="0">Cash</option>
                                                    <option value="1">
                                                        Account
                                                    </option>
                                                @else
                                                    <option value="0" selected>Cash</option>
                                                @endif
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Paid Amount:</td>
                                        <td> <input type="number" type="number"value="{{ $order->totalWithTax() }}"
                                                readonly step="0.01" min="0" class="form-control form-control-sm"
                                                name="paid_amount" id="paid_amount" required></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Due Amount:</td>
                                        <td> <input type="number" value="0" class="form-control form-control-sm"
                                                min="0" readonly name="due_amount" id="due_amount" required></td>
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
        let tax = {!! $tax !!};
        let service_charge = {!! $service_charge !!};
        var total = {{ $order->total }};
        var net_total = {{ $order->total }};
        var grand_total = {{ $order->totalWithTax() }};
        let couponDictionary = {!! $couponsDictionary !!};
        let coupon_discount = 0;
        let delivery_charge = {!! $delivery_charge !!};
        let is_delivery = {!! $order->is_delivery !!};
        let discount = 0;
        let order_non_couponable_discount_amount = {!! $order_non_couponable_discount_amount !!};
        let order_couponable_discount_amount = {!! $order_couponable_discount_amount !!};
        calculateSetServiceChargeAndTax();
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
            //For Delivery
            $('#is_delivery').on('change', function() {
                let destination = $(this).val();
                if (destination == 1) {
                    is_delivery = 1;
                    $('#delivery-charge').show();
                } else {
                    $('#delivery-charge').hide();
                    is_delivery = 0;
                }
                calculateSetServiceChargeAndTax(discount);
            });
        });



        //For Coupon Discount
        $('#coupon_id').on('change', function() {
            coupon_discount = 0;
            if ($(this).val()) {
                coupon_discount = couponDictionary[$(this).val()];
                if (coupon_discount >=  order_couponable_discount_amount) {
                    coupon_discount =  order_couponable_discount_amount;
                    $('#discount').attr('max', order_non_couponable_discount_amount);
                } else {
                    coupon_discount = coupon_discount;
                    $('#discount').attr('max',
                        order_non_couponable_discount_amount + order_couponable_discount_amount  - coupon_discount);
                }
            } else {
                $('#discount').attr('max', total);

            }
            resetAppliedDiscount();
            calculateSetServiceChargeAndTax();

        });
        //for applying coupon discount
        function applyCouponDiscount(discount) {
            if (discount < total) {
                net_total = total - discount;

            } else {
                net_total = 0;

            }

            $('#grand-total').text(foramtValue(net_total));
            $('#discount').attr('max', net_total);
            resetAppliedDiscount();
            calculateSetServiceChargeAndTax()

        }
         //for applying packaging charge
         $('#apply-charge').on('click', function(e) {
            let delivery = parseFloat($('#delivery').val());
            if (isNaN(delivery)) {
                delivery = 0;
            }
            if ($('#delivery')[0].checkValidity()) {

                $('#delivery_charge').val(delivery);

                calculateSetServiceChargeAndTax(discount);

            } else {

                $("#delivery")[0].reportValidity();
            }

        });
        //for resseting applied discount
        function resetAppliedDiscount() {
            $('#discount').val(0);
            $('#discount-amount').val(0);
        }

        //Calculate
        function calculateSetServiceChargeAndTax(discount = 0) {
            let deliveryCharge = (is_delivery) ? parseFloat($('#delivery_charge').val()) : 0;
            discount=$('#discount-amount').val() ;
            let temp_total = total - coupon_discount - discount;
            if (temp_total + deliveryCharge >= 0) {
                let service_charge_amount = parseFloat((parseFloat((service_charge / 100) * temp_total)).toFixed(2));
                let tax_amount = parseFloat(((parseFloat(temp_total) + parseFloat(service_charge_amount)) * (
                        tax / 100))
                    .toFixed(2));

                grand_total = ((temp_total + service_charge_amount) + tax_amount + deliveryCharge).toFixed(2);

                $('#service-charge').text(foramtValue(service_charge_amount));
                $('#tax-amount').text(foramtValue(tax_amount));
                $('#grand-total').text(foramtValue(grand_total));
                $('#paid_amount').val(grand_total);
                $('#payment_type').trigger('change');


            } else {
                alert('Discount cannot be greater than total');

            }
        }
        $(window).keydown(function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                $('#apply-discount').trigger('click');
                return false;
            }
        });
        $('#payment_type').change(function() {
            if ($(this).val() != 1) {
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
            if (due) {
                $('#due_amount').val(due.toFixed(2));
            } else {
                $('#due_amount').val(grand_total);

            }

        });
        $('#paid_amount').focusout(function() {
            if ($(this).val()) {

            } else {
                $(this).val(0);
            }
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
                        $('#table-items').append(
                            "<tr><td colspan='3'>Total</td><td>" +
                            foramtValue(data.order.total) + "</td></tr>");
                        if (data.order.discount && data.order.discount != 0) {
                            $('#table-items').append(
                                "<tr><td colspan='3'>Discount</td><td>" +
                                foramtValue(data.order.discount) + "</td></tr>");
                        }

                        if (data.order.service_charge && data.order.service_charge != 0) {
                            $('#table-items').append(
                                "<tr><td colspan='3'>Service Charge</td><td>" +
                                foramtValue(data.order.service_charge) + "</td></tr>");
                        }
                        if (data.order.tax && data.order.tax != 0) {
                            $('#table-items').append(
                                "<tr><td colspan='3'>Tax</td><td>" +
                                foramtValue(data.order.tax) + "</td></tr>");
                        }
                        if (data.order.delivery_charge && data.order.delivery_charge != 0) {
                            $('#table-items').append(
                                "<tr><td colspan='3'>Packaging Charge</td><td>" +
                                foramtValue(data.order.delivery_charge) + "</td></tr>");
                        }
                        if (data.order.net_total) {
                            $('#table-items').append(
                                "<tr><td colspan='3'>Net Total</td><td>" +
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
            $('#payment-type').html('');
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

        // function calculateSetServiceChargeAndTax(discount) {

        //     let net_total = parseFloat(total) - discount;
        //     if (net_total >= 0) {
        //         let service_charge_amount = parseFloat((parseFloat((service_charge / 100) * net_total)).toFixed(2));
        //         let tax_amount = parseFloat(((parseFloat(net_total) + parseFloat(service_charge_amount)) * (tax / 100))
        //             .toFixed(2));

        //         grand_total = ((net_total + service_charge_amount) + tax_amount).toFixed(2);

        //         $('#service-charge').text(foramtValue(service_charge_amount));
        //         $('#tax-amount').text(foramtValue(tax_amount));
        //         $('#grand-total').text(foramtValue(grand_total));
        //         $('#paid_amount').val(grand_total);
        //         $('#due_amount').val(0);


        //     } else {
        //         alert('Discount cannot be greater than total')
        //     }
        // }

        function foramtValue(val) {
            return 'Rs. ' + val;
        }
    </script>
@endsection
