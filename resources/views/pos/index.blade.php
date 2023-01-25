@extends('layouts.pos.poslayout')

@section('content')
    <form action="{{ route('admin.orders.store') }}" id="order-form" method="POST">
        @csrf

        <div class="row">
            <div class="col-lg-8">
                <div class="col-lg-12 col-sm-12 tabs_wrapper">
                    <div class="page-header ">
                        <div class="page-title">
                            {{-- <h1>{{ $guest_menu ? 'Guest Menu' : '' }}</h1>
                        <h1>{{ $guest_menu ? 'Guest Menu' : 'Staff Menu' }}</h1> --}}
                            <h2 class="staff-price" style="display:{{ $guest_menu ? 'none' : 'block' }};">Staff Menu
                            </h2>
                            <h2 class="guest-price" style="display:{{ $guest_menu ? 'block' : 'none' }};">
                                Guest Menu</h2>
                            <h3 id="menu-type"></h3>
                        </div>
                        <div class="card-tools mt-1" style="float: left;">
                            <div class="input-group mb-3">
                                <input type="text" id="search-items" class="form-control" placeholder="Search"
                                    style="border-radius: 0.57rem ">
                                {{-- <div class="input-group-append">
                                <button type="button" class="btn input-group-text"><i class="fas fa-search"></i></button>
                            </div> --}}
                            </div>

                        </div>
                    </div>
                    <ul class=" tabs owl-carousel owl-theme owl-product  border-0 ">
                        @include('pos.components.categories', ['categories' => $categories])
                    </ul>
                    <div class="tabs_container">
                        @include('pos.components.food_items', [
                            'categories' => $categories,
                            'guest_menu' => $guest_menu,
                        ])
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-12 ">
                <div class="card card-order">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group ">
                                    <label for="guest_menu">Menu Type</label>
                                    <select name="guest_menu" id="guest_menu" class="form-control">
                                        <option value="1" {{ $guest_menu ? 'selected' : '' }}>Guest Menu
                                        </option>
                                        <option value="0" {{ $guest_menu ? '' : 'selected' }}>Staff Menu
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <a href="javascript:void(0);" class="btn btn-adds" data-bs-toggle="modal"
                                    data-bs-target="#create"><i class="fa fa-plus me-2"></i>Select Customer</a>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label for="destination">Order Destination</label>
                                    <select name="destination" id="destination" class="form-control">
                                        <option value="">None</option>
                                        <option value="Table" {{ old('destination') == 'Table' ? 'selected' : '' }}>
                                            Table
                                        </option>
                                        <option value="Room" {{ old('destination') == 'Table' ? 'selected' : '' }}>
                                            Room
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
                                <div class="form-group ">
                                    <label for="destination_no">Destination No</label>
                                    <input type="text" class="form-control " placeholder="Set Destination No"
                                        id="destination_no" autocomplete="off" name="destination_no" re
                                        value="{{ old('destination_no') }}">
                                    @error('destination_no')
                                        <span class=" text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <div class="split-card">
                        </div>
                        <div class="card-body pt-0">
                            <div class="totalitem">
                                <h4>Total items : <i id="cart-item-quantity">0</i></h4>
                                <a href="javascript:void(0);" id="clear-cart-items">Clear all</a>
                            </div>
                            <div class="product-table" id="order-list">
                            </div>
                        </div>
                        <div class="split-card">
                            <div class="col-12">
                                <a href="javascript:void(0);" class="btn btn-adds" data-bs-toggle="modal"
                                    data-bs-target="#couponModal">Add Coupon and Note</a>
                            </div>
                        </div>
                        <div class="card-body pt-0 pb-2">
                            <div class="setvalue">
                                <ul>
                                    <li>
                                        <h5>Subtotal </h5>
                                        <h6 id="totalAmount">Rs. 0</h6>
                                    </li>
                                    <li>
                                        <h5>Discount:</h5>
                                        <h5 id="order_discout">Rs. 0</h5>
                                    </li>
                                    <li>
                                        <h5>Packing Charge:</h5>
                                        <h5 id="order_packing_charge">Rs. 0</h5>
                                    </li>
                                    @if ($service_charge)
                                        <li>
                                            <h5>Service Charge:</h5>
                                            <h5 id="service-charge">Rs. 0</h5>
                                        </li>
                                    @endif
                                    @if ($tax)
                                        <li>
                                            <h5>Tax:</h5>
                                            <h5 id="tax-amount">Rs. 0</h5>
                                        </li>
                                    @endif

                                    <li class="total-value">
                                        <h5>Total </h5>
                                        <h6 id="grand-total">Rs. 0</h6>
                                    </li>
                                </ul>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Payment Type:</label>
                                        <select name="payment_type" class="form-control" id="payment_type" required="">
                                            <option value="0" selected>Cash</option>
                                            <option value="1" {{ $default_customer_type_id != 2 ? 'disabled' : '' }}>
                                                Account</option>
                                        </select>
                                        @error('payment_type')
                                            <span class=" text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Paid Amount:</label>
                                        <input type="number" value="0" readonly step="0.01" min="0"
                                            class="form-control" name="paid_amount" id="paid_amount" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Due Amount:</label>
                                        <input type="number" value="0" class="form-control" min="0"
                                            readonly name="due_amount" id="due_amount" required>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="checkout" id="checkout_value" value="1">



                        </div>
                        <div class="row text-center card-body">
                            <button id="order-checkout" rel="1" class="btn btn-totallabel order-checkout col-12"
                                type="submit">Checkout</button>
                            <div class="btn-pos">
                                <ul>
                                    <li>
                                        <button class="btn btn-pos order-checkout" type="submit" rel="0"><img
                                                src="{{ asset('assets/img/icons/pause1.svg') }}" alt="img"
                                                class="me-1">Save</button>
                                    </li>

                            </div>
                        </div>


                    </div>
                </div>
            </div>
            @include('pos.components.customer_modal')
            @include('pos.components.coupon_modal')
    </form>
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
        // Select 2
        $('#oldCustomer').select2({
            dropdownParent: $('#create')
        });

        $(document).on('click', '.inc.button', function() {
            var $this = $(this),
                $input = $this.prev('input'),
                $parent = $input.closest('div'),
                newValue = parseInt($input.val()) + 1,
                item = $(this).attr('rel');
            var quantity = newValue;
            if (Number.isInteger(quantity) && quantity !== 0) {
                if (item, quantity) {
                    updateItemQuantity(item, quantity);
                    if (newValue >= 2) {
                        $this.siblings('.dec.button').attr('disabled', false);
                    }
                    $input.val(newValue);
                    newValue += newValue;
                } else {
                    console.log('No Item Found');
                }
            } else {
                sweetAlert('Number is not Valid', "Please Enter Valid Quantity", 'warning');
            }

        });
        $(document).on('click', '.dec.button', function() {
            var $this = $(this),
                $input = $this.next('input'),
                $parent = $input.closest('div'),
                item = $(this).attr('rel');
            newValue = parseInt($input.val()) - 1;
            var quantity = newValue;
            if (Number.isInteger(quantity) && quantity !== 0) {
                if (item, quantity) {
                    updateItemQuantity(item, quantity);
                    if (newValue < 2) {
                        $this.attr('disabled', 'disabled');
                    }
                    $input.val(newValue);
                    newValue += newValue;
                } else {
                    console.log('No Item Found');
                }
            } else {
                sweetAlert('Number is not Valid', "Please Enter Valid Quantity", 'warning');
            }

        });

        $('ul.tabs li').click(function() {
            var $this = $(this);
            var $theTab = $(this).attr('id');
            if ($this.hasClass('active')) {
                // do nothing
            } else {
                $this.closest('.tabs_wrapper').find('ul.tabs li, .tabs_container .tab_content').removeClass(
                    'active');
                $('.tabs_container .tab_content[data-tab="' + $theTab + '"], ul.tabs li[id="' + $theTab + '"]')
                    .addClass('active');
            }

        });
        $('#all-items').click(function() {
            $('.tabs_wrapper').find('.tabs_container .tab_content').addClass(
                'active');
        });


        $('#guest_menu').change(function() {
            $.ajax({
                type: 'GET',
                url: '{{ route('admin.cart.clearCartItem') }}',
                success: function(data) {
                    if (data.status == "success") {
                        $('#order-list').html('');
                        $('#cart-item-quantity').text(data.order_count);

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
        $('#clear-cart-items').click(function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to remove all items from cart?",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        url: '{{ route('admin.cart.clearCartItem') }}',
                        success: function(data) {
                            if (data.status == "success") {
                                $('#order-list').html('');
                                $('#cart-item-quantity').text(data.order_count);
                                setTotal(data.total, data.discountable_amount, data
                                    .non_discountable_amount);

                            }
                        }
                    });
                }
            });

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
                    $('.menu-items').css('cssText', 'display: none !important');

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
            let guest_menu = $("#guest_menu").val();
            $.ajax({
                type: 'GET',
                url: '{{ route('admin.cart.addCartItem') }}',
                data: {
                    'item_id': itemId,
                    'guest_menu': guest_menu,
                },
                success: function(data) {
                    btn.attr('disabled', false);
                    $('#cart-item-quantity').text(data.order_count);
                    if (data.status === 'success') {
                        for (var item in data.items) {
                            if ($('#item-' + item).length) {
                                $('#item-' + item).replaceWith(tableRowTemplate(data.items[item]
                                    .id, data.items[
                                        item].name, data.items[item].price, data
                                    .items[item]
                                    .quantity, data.items[item].attributes.image));
                            } else {
                                $('#order-list').append(tableRowTemplate(data.items[item]
                                    .id, data.items[
                                        item].name, data.items[item].price, data
                                    .items[item]
                                    .quantity, data.items[item].attributes.image));
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
                                $('#cart-item-quantity').text(data.order_count);
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
        function tableRowTemplate(id, name, price, quantity = '1', image) {
            return `<ul class="product-lists" id="item-${id}" data-id="${id}"> <li> <div class="productimg"> <div class="productimgs"> <img src="${image}" alt="img"></div>
                <div class="productcontet"> <h4>${name}</h4> <div class="increment-decrement"> <div class="input-groups"> <input type="button" value="-" class="button-minus dec button" rel="${id}" disabled> <input type="text" name="child" value="${quantity}" id="item-quantity-${id}" class="quantity-field" disabled> <input type="button" value="+" rel="${id}" class="button-plus inc button "> </div> </div> </div> </div> </li>
                <li id="item-price-${id}">${price}</li> <li>
                <a class="tr-remove" rel="${id}" href="javascript:void(0);"><img src="{{ asset('assets/img/icons/delete-2.svg') }}" alt="img"></a> </li> </ul>`;
        }
        //Template of table row
        // function tableRowTemplate(id, name, price, quantity = '1') {
        //     return '<tr id="item-' + id + '" data-id="' + id + '"><td width="200px">' + name +
        //         '</td><td class="form-inline col-xs-2" width="250px"><input type="number" id="item-quantity-' + id +
        //         '" class="form-control form-control-sm"  step="1" min="1" value="' +
        //         quantity +
        //         '"><button type="button" class="btn btn-outline-light update-quantity ml-2 btn-sm" rel="' + id +
        //         '"><i class="fa fa-edit"></i></button></td><td>Rs. ' +
        //         price + '</td><td><button type="button" class="btn btn-danger btn-sm tr-remove" rel="' +
        //         id + '">Remove</button></td></tr>';
        // }
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
                        $('#cart-item-quantity').text(data.order_count);
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
        $('.owl-carousel').owlCarousel({
            loop: false,
            nav: true,
            margin: 10,

            responsive: {
                0: {
                    items: 3
                },
                600: {
                    items: 5
                },
                1000: {
                    items: 7
                }
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
            btn.parents('ul').remove();
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
            discount = ($('#discount-amount').val()) ? $('#discount-amount').val() : 0;
            let total_discount = parseFloat(discount) + parseFloat(coupon_discount);
            let temp_total = non_discountable_amount + discountable_amount - coupon_discount - discount;
            if (temp_total >= 0) {
                let service_charge_amount = parseFloat((parseFloat((service_charge / 100) * temp_total)).toFixed(2));
                let tax_amount = parseFloat(((parseFloat(temp_total) + parseFloat(service_charge_amount)) * (tax / 100))
                    .toFixed(2));
                grand_total = ((temp_total + service_charge_amount) + tax_amount + deliveryCharge).toFixed(2);

                $('#service-charge').text(foramtValue(service_charge_amount));
                $('#order_discout').text(foramtValue(total_discount));
                $('#tax-amount').text(foramtValue(tax_amount));
                $('#grand-total').text(foramtValue(grand_total));
                $('#paid_amount').val(grand_total);
                $('#order_packing_charge').text(foramtValue(deliveryCharge));

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
        $('.order-checkout').click(function() {
            console.log($(this).attr('rel'));

            if ($(this).attr('rel') == 1) {
                $('#checkout_value').attr('disabled', false);
            } else {
                $('#checkout_value').attr('disabled', 'disabled');
            }
        });
        $("form").validate({
            submitHandler() {
                const form = document.body.querySelector('#order-form');
                if (form.checkValidity && !form.checkValidity()) {
                    $('#create').modal('show');
                } else {
                    form.submit();

                }
                // Submit and hide form safely

            }
        });



        function foramtValue(val) {
            return 'Rs. ' + val;
        }
    </script>
    @component('admin.orders.components._add_customer_js')
    @endcomponent
@endsection
