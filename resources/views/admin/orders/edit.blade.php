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

            <form action="{{ route('admin.orders.update', $order->id) }}" id="order-form" method="POST">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Edit Order</h2>
                        <div class="card-tools">
                            <a class="btn btn-primary" href="{{ route('admin.orders.index') }}"> Back</a></i></a>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-12">
                                <h3>Customer Info</h3>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group  ml-n2">
                                    <label for="customer_type">Customer Type</label>
                                    <select name="customer_type" id="customer_type" class="form-control" >
                                        <option value="" {{ $order->customer->is_staff === null ? 'selected' : '' }}>
                                            Walking Cutomer</option>
                                        <option value="1" {{ $order->customer->is_staff === 1 ? 'selected' : '' }}>
                                            Staff</option>
                                        <option value="0" {{ $order->customer->is_staff === 0 ? 'selected' : '' }}>
                                            Patient</option>
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
                                        <option value="{{ $customer->id }}" {{ ($order->customer_id === $customer->id) ?'selected':''  }}>
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
                                    <select value="" name="destination" class="form-control" >
                                        <option value="" {{ $order->destination == null ? 'selected' : '' }}>None
                                        </option>
                                        <option value="table" {{ $order->destination == 'table' ? 'selected' : '' }}>Table
                                        </option>
                                        <option value="room" {{ $order->destination == 'room' ? 'selected' : '' }}>Room
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


                        </div>


                        <div class="row">
                            <h3>Order List</h3>
                            <table class="table table-hover table-sm" style="  min-height: 20vh; ">
                                @foreach ($orderItems as $order_no => $items)
                                    <thead>
                                        <th>Order Slip {{ $order_no }}</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </thead>
                                    @foreach ($items as $item)
                                        <tr>
                                            <td width="200px">{{ $item->item->name }}</td>
                                            <td width="200px" class="form-inline  col-xs-2">
                                                @if ($item->total > 1)
                                                    <input type="number" id="order-item-quantity-{{ $item->id }}"
                                                        max="{{ $item->total }}" class="form-control form-control-sm"
                                                        step="1" min="0" value="{{ $item->total }}">
                                                    <button type="button"
                                                        class="btn btn-outline-light update-item-quantity  ml-2 btn-sm"
                                                        rel="{{ $item->id }}"><i class="fa fa-edit"></i>
                                                    </button>
                                                @else
                                                    1
                                                @endif
                                            </td>
                                            <td width="200px">Rs {{ $item->price }}</td>
                                            <td width="200px"><button type="button"
                                                    class="btn btn-sm btn-danger delete-ordered-item"
                                                    rel="{{ $item->id }}"><i class="fa fa-trash"></i>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                <thead>
                                    <th>Order Slip {{ isset($order_no) ? $order_no + 1 : 1 }}</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </thead>
                                <tbody id="order-list">

                                </tbody>
                                <tr>
                                    <td colspan="3">Total</td>
                                    <td> <b id="totalAmount">Rs. {{ $order->total }}</b></td>
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
                                        <td class="btn-group"><input id="discount" value="0" max="{{ $order->total }}"
                                                step=".01" class="form-control form-control-sm " type="number">
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
                                    <tr>
                                        <td colspan="3">Grand Total:</td>
                                        <td id="grand-total">Rs. {{ $order->totalWithTax() }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Payment Type:</td>

                                        <td> <select name="payment_type" class="form-control form-control-sm  float-right"
                                                id="payment_type" required="">
                                                @isset($order->customer->is_staff)
                                                <option value="0" >Cash</option>
                                                <option value="1" {{ ($order->customer->is_staff == 0)?'selected': ''}}>Account
                                                </option>
                                                @else
                                                    <option value="0" selected>Cash</option>
                                                @endisset
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Paid Amount:</td>
                                        <td> <input type="number"value="{{ ($order->customer->is_staff ==0)? 0:$order->totalWithTax() }}" {{ ($order->customer->is_staff == 0)?'': 'readonly'}}
                                                step="0.01" min="0" class="form-control form-control-sm"
                                                name="paid_amount" id="paid_amount" required></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Due Amount:</td>
                                        <td> <input type="number" value="{{ ($order->customer->is_staff ==0)? $order->totalWithTax():0 }}" class="form-control form-control-sm"
                                                min="0" readonly name="due_amount" id="due_amount" required></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row text-center">
                            <div class="col-12 ">
                                <button id="order-checkout" name="checkout" value="1"
                                    class="btn btn-primary ">Checkout</button>

                                <button id="order-submit" class="btn btn-primary ">Edit</button>
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
        let cartTotal =0;
        let tax = {!! $tax !!};
        let service_charge = {!! $service_charge !!};
        var total = {{ $order->total }};
        var net_total ={{ $order->total }};
        var grand_total = {{ $order->totalWithTax() }};
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
                        cartTotal =parseFloat(data.total);
                        setTotal();


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

        //For Updating Quantity of Ordered Items
        $(document).on('click', '.update-item-quantity', function() {
            var btn = $(this);
            var item = $(this).attr('rel');
            var quantity = parseInt($('#order-item-quantity-' + item).val());
            var max = $('#order-item-quantity-' + item).attr('max');

            if (Number.isInteger(quantity) && quantity <= max && quantity > 0) {
                if (item) {
                    $.ajax({
                        type: 'PUT',
                        url: '{{ route('admin.order_items.index') }}' + '/' + item,
                        data: {
                            '_token': $('meta[name="csrf-token"]').attr('content'),
                            'quantity': quantity
                        },
                        success: function(data) {
                            if (data.status === 'success') {
                                orderTotal = parseFloat(data.total);
                                setTotal();

                                sweetAlert('Success', data.message, 'success');
                                if (quantity <= 1) {
                                    btn.closest('td').html(quantity);
                                } else {
                                    btn.closest('td').html(
                                        "<input type='number' id='order-item-quantity-" +
                                        item + "' max='" + quantity +
                                        "' class='form-control' step='1' min='1' value='" +
                                        quantity +
                                        "'><button type='button' class='btn btn-outline-light update-item-quantity  ml-2 btn-sm' rel='" +
                                        item +
                                        "'><i class='fa fa-edit'></i></button>");
                                }
                            } else {
                                sweetAlert('Error', data.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            console.log('Internal Server Error')
                        }
                    });
                } else {
                    console.log('No Item Found');
                }
            } else {
                sweetAlert('Quantity is not Valid', 'Please Enter Valid Quantity', 'warning');
            }
        });

        //For Deleting Quantity of Ordered Items
        $(document).on('click', '.delete-ordered-item', function() {
            let btn = $(this);
            var item = $(this).attr('rel');

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete item form ordered list?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (item) {
                        $.ajax({
                            type: 'DELETE',
                            url: '{{ route('admin.order_items.index') }}' + '/' + item,
                            data: {
                                '_token': $('meta[name="csrf-token"]').attr('content'),
                            },
                            success: function(data) {
                                if (data.status === 'success') {
                                    orderTotal = parseFloat(data.total);
                                    btn.closest('tr').html('');
                                    setTotal();

                                } else {
                                    sweetAlert('Error', data.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                console.log('Internal Server Error')
                            }
                        });
                    } else {
                        console.log('No Item Found');
                    }
                } else {
                    sweetAlert('Number is not Valid', "Please Enter Valid Quantity", 'warning');
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
                                removeItem(btn, item_id);
                                cartTotal =parseFloat(data.total);
                                setTotal();


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
        function setTotal() {
            total = cartTotal + orderTotal;
            $('#coupon_id').trigger('change');
            $('#totalAmount').html('Rs. ' + total);
        }

        //Template of category item
        function template(id, name, price, image) {
            let imagePath = "{{ url('/storage') }}/" + image;

            return '<div class="col-md-6"><div class="card  m-2 "><div class="card-header"><h2 class="card-title">' +
                name + '</h2></div><div class="card-body"><img src="' + imagePath +
                '" width="200px" height="160px" /></div><div class="card-footer d-flex"><div class="col-md-6"><p>Price:Rs ' +
                price + '</p></div><div class="col-md-6"><button type="button" data-id="' +
                id + '" data-price="' + price + '" data-name="' + name +
                '" class="btn btn-primary add-item float-right">Add</button></div></div></div></div>';
        }
        //Template of table row
        function tableRowTemplate(id, name, price, quantity = '1') {
            return '<tr id="item-' + id + '" data-id="' + id + '"><td>' + name +
                '</td><td class="btn-group col-xs-2"><input type="number" id="item-quantity-' + id +
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
                        cartTotal = parseFloat(data.total);
                        setTotal();
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
