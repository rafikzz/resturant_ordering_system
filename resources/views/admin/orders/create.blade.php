@extends('layouts.admin.orders.master')

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <div class="">
                <div class="card-header">
                    <div class="card-tools mt-1" style="float: left;">
                        <div class="input-group mb-3">
                            <input type="text" id="search-items" class="form-control" placeholder="Search"
                                style="border-radius: 0.57rem ">
                            {{-- <div class="input-group-append">
                                <button type="button" class="btn input-group-text"><i class="fas fa-search"></i></button>
                            </div> --}}
                        </div>

                    </div>
                    <div class="card-tools">

                        <div class="form-group">
                            <select class="form-control " id="category">
                                <option selected value="">All</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-body " id="menu-items">

                    @foreach ($categories as $category)
                        <div class="row menu-category" data-category="{{ $category->id }}">
                            <div class="col-12">
                                <b>
                                    <h4>{{ $category->title }}</h4>
                                </b>
                            </div>
                            @foreach ($category->active_items as $item)
                                @component('admin.orders.components._menu-items', ['item' => $item, 'guest_menu' => $guest_menu])
                                @endcomponent
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <form action="{{ route('admin.orders.store') }}" id="order-form" method="POST">
                @csrf
                <div class="card ">
                    <div class="overlay" id="overlay" style="display: none">
                        <i class="fas fa-2x fa-sync fa-spin"></i>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 ml-n2">
                                <h3>Menu Type</h3>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="guest_menu" id="guest_menu1"
                                    value="1" checked>
                                <label class="form-check-label" for="guest_menu1">Guest Menu</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="guest_menu" id="guest_menu2"
                                    value="0">
                                <label class="form-check-label" for="guest_menu2">Staff Menu</label>
                            </div>
                            <div class="col-12 ml-n2">
                                <h3>Customer Info</h3>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group  ml-n2">
                                    <label for="customer_type">Customer Type</label>
                                    <select name="customer_type" id="customer_type" class="form-control">
                                        @foreach ($customer_types as $customer_type)
                                            <option value="{{ $customer_type->id }}"
                                                {{ $customer_type->id == $default_customer_type_id ? 'selected' : '' }}>
                                                {{ $customer_type->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                    @error('customer_type')
                                        <span class=" text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            @component('admin.orders.components._add_customers',
                                ['customers' => $customers, 'customer_type_id' => $default_customer_type_id, 'customer_id' => null])
                            @endcomponent

                            <div class="col-md-6">
                                <div class="form-group  ml-n2">
                                    <label for="destination">Order Destination</label>
                                    <select name="destination" id="destination" class="form-control">
                                        <option value="">None</option>
                                        <option value="Table" {{ old('destination') == 'Table' ? 'selected' : '' }}>Table
                                        </option>
                                        <option value="Room" {{ old('destination') == 'Table' ? 'selected' : '' }}>Room
                                        </option>
                                    </select>
                                    @error('destination')
                                        <span class=" text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group  ml-n2">
                                    <label for="destination_no">Destination No</label>
                                    <input type="text" class="form-control " placeholder="Set Table No"
                                        id="destination_no" autocomplete="off" name="destination_no" re
                                        value="{{ old('destination_no') }}">
                                    @error('destination_no')
                                        <span class=" text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group  ml-n2">
                                    <label for="is_delivery">Packaging</label>
                                    <select name="is_delivery" id="is_delivery"
                                        class="form-control form-control-sm  float-right">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                    @error('is_delivery')
                                        <span class=" text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <h3>Order List</h3>
                            <table class="table table-sm table-hover " style="  min-height: 10vh; ">
                                <thead>
                                    <th>Item Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th width="150px">Action</th>
                                </thead>
                                <tbody id="order-list" style="min-height:20px">

                                </tbody>
                                <tr>
                                    <td colspan="3">Total</td>
                                    <td> <b id="totalAmount">Rs.0</b></td>
                                </tr>
                                <tr class="checkout">
                                    <th colspan="4">Add Checkout Information
                                        <span class="custom-control custom-switch float-right ">
                                            <input type="checkbox" class="custom-control-input" value="1" checked
                                                id="toggle-checkout">
                                            <label class="custom-control-label" for="toggle-checkout"></label></span>
                                    </th>
                                </tr>
                                <tbody id="checkout">
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
                                        <td class="btn-group"><input id="discount" value="0" max="0"
                                                min="0" step=".01" class="form-control form-control-sm "
                                                type="number">
                                            <input type="hidden" id="discount-amount" name="discount">
                                            <button id="apply-discount" type="button"
                                                class="btn btn-primary btn-sm ml-2">Apply</button>
                                        </td>
                                    </tr>
                                    @if ($service_charge)
                                        <tr>
                                            <td colspan="3">Service Charge:</td>
                                            <td id="service-charge">Rs. 0</td>
                                        </tr>
                                    @endif
                                    @if ($tax)
                                        <tr>
                                            <td colspan="3">Tax Amount:</td>
                                            <td id="tax-amount">Rs. 0</td>
                                        </tr>
                                    @endif
                                    <tr id="delivery-charge" style="display:none">
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
                                        <td id="grand-total">Rs. 0</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Payment Type:</td>

                                        <td> <select name="payment_type" class="form-control form-control-sm  float-right"
                                                id="payment_type" required="">
                                                <option value="0" selected>Cash</option>
                                                <option value="1"
                                                    {{ $default_customer_type_id != 2 ? 'disabled' : '' }}>
                                                    Account</option>
                                            </select>
                                            @error('payment_type')
                                                <span class=" text-danger" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Paid Amount:</td>
                                        <td> <input type="number" value="0" readonly step="0.01" min="0"
                                                class="form-control form-control-sm" name="paid_amount" id="paid_amount"
                                                required></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Due Amount:</td>
                                        <td> <input type="number" value="0" class="form-control form-control-sm"
                                                min="0" readonly name="due_amount" id="due_amount" required></td>
                                    </tr>
                                </tbody>

                            </table>
                        </div>
                        <hr />
                        <div class="row text-center">
                            <div class="col-12 ">
                                <button id="order-checkout" name="checkout" value="1"
                                    class="btn btn-primary submit-btn">Checkout</button>

                                <button id="order-submit submit-btn" class="btn btn-primary ">Save</button>
                            </div>
                        </div>
                    </div>
            </form>

        </div>
    </div>
@endsection
@section('js')
    <script>
        let tax = {!! $tax !!};
        let service_charge = {!! $service_charge !!};
        var total = 0;
        let is_delivery = 0;
        var net_total = 0;
        var grand_total = 0;
        let couponDictionary = {!! $couponsDictionary !!};
        let coupon_discount = 0;
        let discount = 0;
        let discountable_amount = 0;
        let non_discountable_amount = 0;
        let delivery_charge = {!! $delivery_charge !!};

        $('input[type=radio][name=guest_menu]').change(function() {
            $.ajax({
                type: 'GET',
                url: '{{ route('admin.cart.clearCartItem') }}',
                success: function(data) {
                    if (data.status == "success") {
                        $('#order-list').html('');
                        setTotal(data.total, data.discountable_amount, data.non_discountable_amount);

                    }
                }
            });
            if (this.value == 1) {
                $('.staff-price').css('display', 'none');
                $('.guest-price').css('display', 'block');

            } else if (this.value == 0) {
                $('.staff-price').css('display', 'block');
                $('.guest-price').css('display', 'none');
            }
        });

        $('#toggle-checkout').click(function() {
            if ($(this).is(':checked')) {
                $('#checkout').show();
                $('#order-checkout').show().attr('disabled', false);

            } else {

                $('#checkout').hide();
                $('#order-checkout').hide().attr('disabled', 'disabled');
            }
        });
        $(function() {

            //For Coupon Discount
            $('#coupon_id').on('change', function() {
                coupon_discount = 0;
                if ($(this).val()) {
                    coupon_discount = couponDictionary[$(this).val()];
                    if (coupon_discount >= discountable_amount) {
                        coupon_discount = discountable_amount;
                        $('#discount').attr('max', non_discountable_amount);
                    } else {
                        coupon_discount = coupon_discount;
                        $('#discount').attr('max', non_discountable_amount + discountable_amount -
                            coupon_discount);
                    }
                } else {
                    $('#discount').attr('max', total);

                }
                resetAppliedDiscount();
                calculateSetServiceChargeAndTax();

            });
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

            //Getting Items on changing category
            $('#category').on('change', function() {
                let category_id = $(this).val();
                if (category_id) {
                    $('.menu-category').hide();
                    $('.menu-category[data-category="' + category_id + '"]').show();

                } else {
                    $('.menu-category').show();
                }
            });
            //Getting Items on changing category
            $('#search-items').on('keyup', function() {
                let search = $(this).val();
                if (search) {
                    $('.menu-items').hide();
                    $('.menu-items').filter(function() {
                        return $(this).text().toLowerCase().indexOf(search.toLowerCase()) >= 0;
                    }).show();
                } else {
                    $('.menu-items').show();
                }
            });
        });


        //For Adding Order Item to List
        $(document).on('click', '.add-item', function() {
            let btn = $(this);
            btn.attr('disabled', 'disabled');
            let itemId = $(this).data('id');
            let itemName = $(this).data('name');
            let itemPrice = $(this).data('price');
            let guest_menu = $("#guest_menu1").is(":checked") ? 1 : 0;;
            $.ajax({
                type: 'GET',
                url: '{{ route('admin.cart.addCartItem') }}',
                data: {
                    'item_id': itemId,
                    'guest_menu': guest_menu,
                },
                success: function(data) {
                    btn.attr('disabled', false);
                    if (data.status === 'success') {
                        for (var item in data.items) {
                            if ($('#item-' + item).length) {
                                $('#item-' + item).replaceWith(tableRowTemplate(data.items[item]
                                    .id, data.items[
                                        item].name, data.items[item].price, data
                                    .items[item]
                                    .quantity));
                            } else {
                                $('#order-list').append(tableRowTemplate(data.items[item]
                                    .id, data.items[
                                        item].name, data.items[item].price, data
                                    .items[item]
                                    .quantity));
                            }
                        }
                        // sweetAlert('Success',data.message,'success');
                        setTotal(data.total, data.discountable_amount, data.non_discountable_amount);
                    }
                },
                error: function(xhr) {
                    btn.attr('disabled', false);

                    console.log('Internal Server Error')
                }
            });
        });
        //For Updating Quantity of Items
        $(document).on('click', '.update-quantity', function() {
            var item = $(this).attr('rel');
            var quantity = parseInt($('#item-quantity-' + item).val());
            $('#item-quantity-' + item).val(quantity);
            if (Number.isInteger(quantity) && quantity !== 0) {
                if (item, quantity) {
                    updateItemQuantity(item, quantity);
                } else {
                    console.log('No Item Found');
                }
            } else {
                sweetAlert('Number is not Valid', "Please Enter Valid Quantity", 'warning');
            }
        });

        //For Deleting Cart Item
        $(document).on('click', '.tr-remove', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to remove this item?",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let btn = $(this);
                    let item_id = $(this).attr('rel');
                    $.ajax({
                        type: 'GET',
                        url: '{{ route('admin.cart.removeCartItem') }}',
                        data: {
                            'item_id': item_id
                        },
                        success: function(data) {
                            if (data.status === 'success') {
                                removeItem(btn, item_id);
                                setTotal(data.total, data.discountable_amount, data
                                    .non_discountable_amount);
                            } else {
                                alert(data.message);
                            }
                        },
                        error: function(xhr) {
                            alert('Internal Server Error')
                        }
                    });
                }
            });
        });



        //For clearing Category Items
        function clearCategoryItems() {
            $('#category-items').html('');
        }
        //For setting the total
        function setTotal(totalAmount, discountableAmount = 0, nonDiscountableAmount = 0) {

            discountable_amount = discountableAmount;
            non_discountable_amount = nonDiscountableAmount;
            total = totalAmount;
            resetAppliedDiscount();
            if ($('#coupon_id').val()) {
                coupon_discount = couponDictionary[$('#coupon_id').val()];
                if (coupon_discount >= discountable_amount) {
                    coupon_discount = discountable_amount;
                    $('#discount').attr('max', non_discountable_amount);
                } else {
                    coupon_discount = coupon_discount;
                    $('#discount').attr('max', non_discountable_amount + discountable_amount - coupon_discount);
                }
            } else {
                $('#discount').attr('max', total);

            }

            $('#totalAmount').html('Rs. ' + totalAmount);
            calculateSetServiceChargeAndTax();

        }

        //Template of table row
        function tableRowTemplate(id, name, price, quantity = '1') {
            return '<tr id="item-' + id + '" data-id="' + id + '"><td width="200px">' + name +
                '</td><td class="form-inline col-xs-2" width="250px"><input type="number" id="item-quantity-' + id +
                '" class="form-control form-control-sm"  step="1" min="1" value="' +
                quantity +
                '"><button type="button" class="btn btn-outline-light update-quantity ml-2 btn-sm" rel="' + id +
                '"><i class="fa fa-edit"></i></button></td><td>Rs. ' +
                price + '</td><td><button type="button" class="btn btn-danger btn-sm tr-remove" rel="' +
                id + '">Remove</button></td></tr>';
        }
        //For Sending Update Item to the server
        function updateItemQuantity(item_id, quantity) {
            $.ajax({
                type: 'GET',
                url: '{{ route('admin.cart.editCartItemQuantity') }}',
                data: {
                    'item_id': item_id,
                    'quantity': quantity

                },
                success: function(data) {
                    if (data.status === 'success') {

                        setTotal(data.total, data.discountable_amount, data.non_discountable_amount);
                    } else {
                        console.log(data.message);
                    }
                },
                error: function(xhr) {
                    console.log('Internal Server Error')
                }
            });
        }
        //for applying discount
        $('#apply-discount').on('click', function(e) {
            discount = parseFloat($('#discount').val());
            if (isNaN(discount)) {
                discount = 0;
            }
            if ($('#discount')[0].checkValidity()) {

                $('#discount-amount').val(discount);

                calculateSetServiceChargeAndTax(discount);

            } else {

                $("#discount")[0].reportValidity();
            }

        });

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

        function applyCouponDiscount(discount) {
            if (discount < total) {
                net_total = total - discount;

            } else {
                net_total = 0;

            }

            $('#grand-total').text(foramtValue(net_total));


        }
        //for resseting applied discount
        function resetAppliedDiscount() {
            $('#discount').val(0);
            $('#discount-amount').val(0);
        }
        //For removing Item in the row
        function removeItem(btn, item_id) {
            btn.parents('tr').remove();
        }
        //For Sweet Alert
        function sweetAlert(title, text, icon) {
            Swal.fire({
                title: title,
                text: text,
                icon: icon,
            });
        }

        function calculateSetServiceChargeAndTax(discount = 0) {
            let deliveryCharge = (is_delivery) ? parseFloat($('#delivery_charge').val()) : 0;
            discount=$('#discount-amount').val() ;
            console.log(discount);
            let temp_total = non_discountable_amount + discountable_amount - coupon_discount -discount;
            if (temp_total >= 0) {
                let service_charge_amount = parseFloat((parseFloat((service_charge / 100) * temp_total)).toFixed(2));
                let tax_amount = parseFloat(((parseFloat(temp_total) + parseFloat(service_charge_amount)) * (tax / 100))
                    .toFixed(2));
                grand_total = ((temp_total + service_charge_amount) + tax_amount + deliveryCharge).toFixed(2);

                $('#service-charge').text(foramtValue(service_charge_amount));
                $('#tax-amount').text(foramtValue(tax_amount));
                $('#grand-total').text(foramtValue(grand_total));
                $('#paid_amount').val(grand_total);
                $('#payment_type').trigger('change');


            } else {
                alert('Discount cannot be greater than total')
            }
        }
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
            if ($(this).val()) {} else {
                $(this).val(0);
            }
        });


        function foramtValue(val) {
            return 'Rs. ' + val;
        }
    </script>
    @component('admin.orders.components._add_customer_js')
    @endcomponent
@endsection
