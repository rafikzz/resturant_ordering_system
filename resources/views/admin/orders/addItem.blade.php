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
                                @component('admin.orders.components._menu-items', ['item' => $item])
                                @endcomponent
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <form action="{{ route('admin.orders.addItem', $order->id) }}" id="order-form" method="POST">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="overlay" id="overlay" style="display: none">
                        <i class="fas fa-2x fa-sync fa-spin"></i>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group  ml-n2">
                                    <label for="customer_id"> Customer</label>
                                    <input type="text" class="form-control" placeholder="Set Table No" id="customer_id"
                                        autocomplete="off" name="customer_id"
                                        value="{{ $order->customer->name }} ({{ $order->customer->phone_no }})" required
                                        readonly>
                                    @error('customer_id')
                                        <span class=" text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group  ml-n2">
                                    <label for="customer_type">Customer Type</label>
                                    <select name="customer_type" id="customer_type" class="form-control" disabled>
                                        @foreach ($customer_types as $customer_type)
                                            <option value="{{ $customer_type->id }}"
                                                {{ $order->customer->customer_type_id == $customer_type->id ? 'selected' : '' }}>
                                                {{ $customer_type->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group  ml-n2">
                                    <label for="destination">Order Destination</label>
                                    <select value="" name="destination" class="form-control" disabled>
                                        <option value="" {{ $order->destination == null ? 'selected' : '' }}>None
                                        </option>
                                        <option value="Table" {{ $order->destination == 'Table' ? 'selected' : '' }}>Table
                                        </option>
                                        <option value="Room" {{ $order->destination == 'Room' ? 'selected' : '' }}>Room
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
                                    <input type="text" class="form-control " placeholder="Set Destination No"
                                        id="destination_no" autocomplete="off" value="{{ $order->destination_no }}"
                                        name="destination_no" value="{{ old('destination_no') }}" readonly>
                                    @error('destination_no')
                                        <span class=" text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <h3>Order List</h3>
                            <table class="table table-hover table-sm" style="  min-height: 20vh; ">
                                @foreach ($orderItems as $order_no => $items)
                                    <thead>
                                        <th>Order Slip {{ $order_no }}</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th width="150px">Action</th>
                                    </thead>
                                    @foreach ($items as $item)
                                        <tr>
                                            <td>{{ $item->item->name }}</td>
                                            <td>{{ $item->total }}</td>
                                            <td>Rs {{ $item->price }}</td>
                                            <td></td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                <thead>
                                    <th>Order Slip {{ isset($order_no) ? $order_no + 1 : 1 }}</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th width="150px">Action</th>
                                </thead>
                                <tbody id="order-list">

                                </tbody>
                                <tr>
                                    <td colspan="3">Total</td>
                                    <td> <b id="totalAmount">Rs. {{ $order->total }}</b></td>
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
                                        <td class="btn-group"><input id="discount" value="0"
                                                max="{{ $order->total }}" step=".01"
                                                class="form-control form-control-sm " type="number">
                                            <input type="hidden" id="discount-amount" name="discount">
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
                                    @if ($delivery_charge)
                                        <tr>
                                            <td colspan="3">Take Packaging Charge</td>
                                            <td><select name="is_delivery" id="is_delivery"
                                                    class="form-control form-control-sm  float-right">
                                                    <option value="0">No</option>
                                                    <option value="1">Yes</option>
                                                </select>
                                                @error('is_delivery')
                                                    <span class=" text-danger" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </td>

                                        </tr>
                                        <tr id="delivery-charge" style="display:none">
                                            <td colspan="3">Packaging Charge Amount:</td>
                                            <td>Rs. {{ $delivery_charge }}</td>
                                        </tr>
                                    @endif

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
                                                    <option value="1"
                                                        {{ $order->customer->customer_type_id == 3 ? 'selected' : '' }}>
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
                                        <td> <input
                                                type="number"value="{{ $order->totalWithTax() }}"
                                                readonly step="0.01" min="0" class="form-control form-control-sm"
                                                name="paid_amount" id="paid_amount" required></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Due Amount:</td>
                                        <td> <input type="number"
                                                value="{{ $order->customer->customer_type_id == 3 ? $order->totalWithTax() : 0 }}"
                                                class="form-control form-control-sm" min="0" readonly
                                                name="due_amount" id="due_amount" required></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row text-center">
                            <div class="col-12 ">
                                <button id="order-checkout" name="checkout" value="1"
                                    class="btn btn-primary ">Checkout</button>

                                <button id="order-submit" class="btn btn-primary ">Add to Order</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
@endsection
@section('js')
    <script>
        let orderTotal = {{ $order->total }};
        let tax = {!! $tax !!};
        let service_charge = {!! $service_charge !!};
        var total = {{ $order->total }};
        var net_total = {{ $order->total }};
        var grand_total = {{ $order->totalWithTax() }};
        let couponDictionary = {!! $couponsDictionary !!};
        let coupon_discount = 0;
        let delivery_charge = {!! $delivery_charge !!};
        let is_delivery = 0;
        let discount = 0;


        $(function() {

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
            $('#toggle-checkout').click(function() {
                if ($(this).is(':checked')) {
                    $('#checkout').show();
                    $('#order-checkout').show().attr('disabled', false);

                } else {

                    $('#checkout').hide();
                    $('#order-checkout').hide().attr('disabled', 'disabled');
                }
            });
            //For Coupon Discount
            $('#coupon_id').on('change', function() {
                coupon_discount = 0;
                if ($(this).val()) {
                    coupon_discount = couponDictionary[$(this).val()];
                    if (coupon_discount >= total) {
                        coupon_discount = 0;
                        $('#coupon_id').val("");
                        sweetAlert('Error', 'Coupon Amount Should Not Be Greater Than Total Amount',
                            'error');

                        $('#discount').attr('max', 0);
                    } else {
                        $('#discount').attr('max', total - coupon_discount);
                    }
                }
                resetAppliedDiscount();
                calculateSetServiceChargeAndTax();

            });

            //For Delivery
            $('#is_delivery').on('change', function() {
                let destination = $(this).val();
                if (delivery_charge) {
                    if (destination == 1) {
                        is_delivery = 1;
                        $('#delivery-charge').show();
                    } else {
                        $('#delivery-charge').hide();
                        is_delivery = 0;
                    }
                    calculateSetServiceChargeAndTax(discount);
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
            $.ajax({
                type: 'GET',
                url: '{{ route('admin.cart.addCartItem') }}',
                data: {
                    'item_id': itemId,
                },
                success: function(data) {
                    btn.attr('disabled', false);

                    if (data.status === 'success') {
                        for (var item in data.items) {
                            if($('#item-'+item).length){
                                $('#item-'+item).replaceWith(tableRowTemplate(data.items[item]
                                .id, data.items[
                                    item].name, data.items[item].price, data
                                .items[item]
                                .quantity));
                            }else{
                                $('#order-list').append(tableRowTemplate(data.items[item]
                                .id, data.items[
                                    item].name, data.items[item].price, data
                                .items[item]
                                .quantity));
                            }


                        }

                        // sweetAlert('Success',data.message,'success');

                        setTotal(data.total);

                    } else {
                        console.log(data.message);
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
                                setTotal(data.total);
                                removeItem(btn, item_id);
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
        function setTotal(totalAmount) {
            total = totalAmount + orderTotal;
            $('#totalAmount').html('Rs. ' + total);
            calculateSetServiceChargeAndTax(discount);
        }
        //Template of category item
        function template(id, name, price, image) {
            let imagePath = "{{ url('/storage') }}/" + image;

            return '<div class="col-md-5"><div class="card  m-2 "><div class="card-header"><h2 class="card-title">' +
                name + '</h2></div><div class="card-body"><img src="' + imagePath +
                '" width="200px" height="160px" /></div><div class="card-footer d-flex"><div class="col-md-6"><p>Price:Rs ' +
                price + '</p></div><div class="col-md-6"><button type="button" data-id="' +
                id + '" data-price="' + price + '" data-name="' + name +
                '" class="btn btn-primary add-item float-right">Add</button></div></div></div></div>';
        }
        //Template of table row
        function tableRowTemplate(id, name, price, quantity = '1') {
            return '<tr id="item-' + id + '" data-id="' + id + '"><td>' + name +
                '</td><td class="form-inline col-xs-2"><input type="number" id="item-quantity-' + id +
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

                        setTotal(data.total);
                    } else {
                        console.log(data.message);
                    }
                },
                error: function(xhr) {
                    console.log('Internal Server Error')
                }
            });
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


        //for applying discount
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

        });
        //for resseting applied discount
        function resetAppliedDiscount() {
            $('#discount').val(0);
            $('#discount-amount').val(0);
        }

        function calculateSetServiceChargeAndTax(discount = 0) {
            let deliveryCharge = (is_delivery) ? parseFloat(delivery_charge) : 0;
            let temp_total = total - coupon_discount - discount;
            if (temp_total + deliveryCharge >= 0) {
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
                alert('Discount cannot be greater than total');
                $('#coupon_id').val("");
            }
        }
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

        function foramtValue(val) {
            return 'Rs. ' + val;
        }
    </script>
@endsection
