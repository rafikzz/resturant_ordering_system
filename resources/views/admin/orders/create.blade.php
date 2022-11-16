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
                                <h6>{{ $category->title }}</h6>
                            </div>
                            @foreach ($category->active_items as $item)
                                <div class="col-md-6 menu-items">
                                    <div class="food-item-card text-center">
                                        <div class="food-item-image">
                                            <img src="{{ $item->image() }}" alt="food-item"></a>
                                        </div>
                                        <div class="food-item-content">
                                            <h6 class="food-item-name">{{ $item->name }}</h6>
                                            <h6 class="food-item-price"><span>Rs. {{ $item->price }}</h6><button
                                                data-id="{{ $item->id }}" data-price="{{ $item->price }}"
                                                data-name="{{ $item->name }}" class="food-item-add  btn-success add-item"
                                                title="Add to Cart"><i class="fas fa-cart-plus"></i><span>
                                                    Add</span></button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <form action="{{ route('admin.orders.store') }}" id="order-form" method="POST">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <h3>Customer Info</h3>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group  ml-n2">
                                    <label for="customer_type">Customer Type</label>
                                    <select name="customer_type" id="customer_type" class="form-control">
                                        <option value="" selected>Walking Cutomer</option>
                                        <option value="1">Staff</option>
                                        <option value="0">Patient</option>
                                    </select>
                                    @error('customer_type')
                                        <span class=" text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 my-2 d-flex">
                                <label for="">New or Existing: </label>
                                <div class="form-check mx-2">
                                    <input class="form-check-input" type="radio" name="new_or_old" id="existingCustomer"
                                        value="existing" {{ old('new_or_old') !== 'new' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="existing">
                                        Existing
                                    </label>
                                </div>
                                <div class="form-check mx-2">
                                    <input class="form-check-input" type="radio" name="new_or_old" id="newCustomer"
                                        value="new" {{ old('new_or_old') == 'new' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="new">
                                        New
                                    </label>
                                </div>
                            </div>
                            <div id="old" class="col-12 form-group  py-3"
                                style="display:  {{ old('new_or_old') !== 'new' ? 'block' : 'none' }}">
                                <select name="customer_id" class="form-control select2" style="width: 50%" id="oldCustomer"
                                    required {{ old('new_or_old') == 'new' ? 'disabled' : '' }}>
                                    <option value="">--Please Select Customer--</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">
                                            {{ $customer->name }}({{ $customer->phone_no }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div id="new" class="col-12  py-3 form-group"
                                style="display: {{ old('new_or_old') === 'new' ? 'block' : 'none' }};">
                                <div class="d-flex">
                                    <div class="col-6">
                                        <label for="customer_name">Customer Name</label>
                                        <input class="form-control" name="customer_name" value="{{ old('customer_name') }}"
                                            type="text" placeholder="Enter Customer Name" autocomplete="off"
                                            {{ old('new_or_old') !== 'new' ? 'disabled' : '' }}>
                                        @error('customer_name')
                                            <span class=" text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="customer_phone_no">Customer Number</label>
                                        <input class="form-control" name="customer_phone_no" type="text"
                                            value="{{ old('customer_phone_no') }}" placeholder="Enter Customer Phone No"
                                            autocomplete="off" {{ old('new_or_old') !== 'new' ? 'disabled' : '' }}>
                                        @error('customer_phone_no')
                                            <span class=" text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group  ml-n2">
                                    <label for="destination">Order Destination</label>
                                    <select name="destination" class="form-control">
                                        <option value="">None</option>
                                        <option value="table">Table</option>
                                        <option value="room">Room</option>
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
                                <tr>
                                    <th colspan="4">Checkout Information</th>
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
                                        <td class="btn-group"><input id="discount" value="0" max="0" min="0"
                                                step=".01" class="form-control form-control-sm " type="number">
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
                                                    {{ old('customer_type') !== 0 || old('customer_type') !== 1 ? 'disabled' : '' }}>
                                                    Account</option>
                                            </select>
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
                                    class="btn btn-primary ">Checkout</button>

                                <button id="order-submit" class="btn btn-primary ">Save</button>
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
        var net_total = 0;
        var grand_total = 0;
        let couponDictionary = {!! $couponsDictionary !!};
        let coupon_discount = 0;


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
            //For Coupon Discount
            $('#coupon_id').on('change', function() {
                coupon_discount = 0;
                if ($(this).val()) {
                    coupon_discount = couponDictionary[$(this).val()];
                }
                applyCouponDiscount(coupon_discount);

            });

            //Getting Customer Type
            $('#customer_type').on('change', function() {
                let customer_type = parseInt($(this).val());
                $("#payment_type option[value='1']").attr("disabled", false);

                if (customer_type === 0 || customer_type === 1) {
                    if(customer_type === 0 )
                    {
                        $("#payment_type").val('1').trigger('change');
                    }

                }
                 else {
                    $("#payment_type").val(0).trigger('change');
                    $("#payment_type option[value='1']").attr("disabled", "disabled");
                    customer_type = null;
                }
                $.ajax({
                    type: 'GET',
                    url: '{{ route('admin.customer.getType') }}',
                    data: {
                        'customer_type': customer_type,
                    },
                    success: function(data) {

                        if (data.status === 'success') {
                            $('#oldCustomer').find('option').not(':first').remove();
                            data.customers.forEach(function(customer) {
                                let text = customer.name + '(' + customer.phone_no +
                                    ')';
                                let newOption = new Option(text, customer.id, true,
                                    true);
                                $('#oldCustomer').append(newOption);
                            });
                            $('#oldCustomer').val(null);

                        } else {
                            console.log(data.message);

                        }
                    },
                    error: function(xhr) {
                        console.log('Internal Sever Error');

                    }
                });

            });
            //Getting Items on changing category
            $('#search-items').on('keyup', function() {
                let search = $(this).val();
                if (search) {
                    $('.menu-items').hide();
                    $('.menu-items').filter(function() {
                        console.log($(this).closest().text().toLowerCase());
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
                        $('#order-list').html('');
                        for (var item in data.items) {
                            $('#order-list').append(tableRowTemplate(data.items[item]
                                .id, data.items[
                                    item].name, data.items[item].price, data
                                .items[item]
                                .quantity));

                        }

                        // sweetAlert('Success',data.message,'success');

                        setTotal(data.total);

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
                    console.log(item_id);
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

        //For Inputing Existing Customer
        $('#existingCustomer').click(function() {
            if ($(this).is(':checked')) {
                $('#old').css('display', 'block');
                $('#new').css('display', 'none');
                $('#old :input').prop('disabled', false);
                $('#new :input').prop('disabled', true);
            }
        });
        //For Inputing New Customer
        $('#newCustomer').click(function() {
            if ($(this).is(':checked')) {
                $('#old').css('display', 'none');
                $('#new').css('display', 'block');
                $('#old :input').prop('disabled', true);
                $('#new :input').prop('disabled', false);
            }
        });

        //For clearing Category Items
        function clearCategoryItems() {
            $('#category-items').html('');
        }
        //For setting the total
        function setTotal(totalAmount) {
            total = totalAmount;
            $('#coupon_id').trigger('change');
            $('#totalAmount').html('Rs. ' + totalAmount);
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
            temp_total = parseFloat(net_total) - discount;
            if (temp_total >= 0) {
                let service_charge_amount = parseFloat((parseFloat((service_charge / 100) * temp_total)).toFixed(2));
                let tax_amount = parseFloat(((parseFloat(temp_total) + parseFloat(service_charge_amount)) * (tax / 100))
                    .toFixed(2));

                grand_total = ((temp_total + service_charge_amount) + tax_amount).toFixed(2);

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

        function foramtValue(val) {
            return 'Rs. ' + val;
        }
    </script>
@endsection
