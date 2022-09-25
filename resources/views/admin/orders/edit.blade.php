@extends('layouts.admin.master')

@section('content')
    <div class="row">
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
                            <input type="hidden" name="order_id" id="orderId" value="{{ $order->id }}" />
                            <div class="col-12 my-2 d-flex">
                                <label for="">Customer Type: </label>
                                <div class="form-check mx-2">
                                    <input class="form-check-input" type="radio" name="customer_type"
                                        id="existingCustomer" value="existing"
                                        {{ old('customer_type') !== 'new' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="existing">
                                        Existing
                                    </label>
                                </div>
                                <div class="form-check mx-2">
                                    <input class="form-check-input" type="radio" name="customer_type" id="newCustomer"
                                        value="new" {{ old('customer_type') == 'new' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="new">
                                        New
                                    </label>
                                </div>
                            </div>
                            <div id="old" class="col-12 form-group  py-3"
                                style="display:  {{ old('customer_type') !== 'new' ? 'block' : 'none' }}">
                                <select name="customer_id" class="form-control select2" style="width: 50%" id="oldCustomer"
                                    required {{ old('customer_type') == 'new' ? 'disabled' : '' }}>
                                    <option value="">--Please Select Customer--</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}"
                                            {{ $customer->id == $order->customer_id ? 'selected' : '' }}>
                                            {{ $customer->phone_no }}({{ $customer->name }})</option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div id="new" class="col-12  py-3 form-group"
                                style="display: {{ old('customer_type') === 'new' ? 'block' : 'none' }};">
                                <div class="d-flex">
                                    <div class="col-6">
                                        <label for="customer_name">Customer Name</label>
                                        <input class="form-control" name="customer_name" value="{{ old('customer_name') }}"
                                            autocomplete="off" type="text" placeholder="Enter Customer Name"
                                            {{ old('customer_type') !== 'new' ? 'disabled' : '' }}>
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
                                            autocomplete="off" {{ old('customer_type') !== 'new' ? 'disabled' : '' }}>
                                        @error('customer_phone_no')
                                            <span class=" text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label for="table_no"> Table No</label>
                                    <input type="text" class="form-control " placeholder="Set Table No" id="table_no"
                                        autocomplete="off" name="table_no"
                                        value="{{ old('table_no') ?: $order->table_no }}" required>
                                    @error('table_no')
                                        <span class=" text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group  ml-n2">
                                    <label for="is_take_away">Type</label>
                                    <select name="is_take_away" required class="form-control">
                                        <option value="0">Dine In</option>
                                        <option value="1"  {{ $order->is_take_away?'selected':'' }}>Take Away</option>
                                    </select>
                                    @error('is_take_away')
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
                            </table>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-6">
                                <b>Total</b>
                            </div>
                            <div class="col-5 ">
                                <b class="float-right" id="totalAmount">Rs. {{ $order->total }}</b>
                            </div>
                        </div>
                        <hr />

                        <div class="row">
                            <div class="col-12 ">
                                <button id="order-submit" class="btn btn-primary float-right">Edit</button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Add Food Item</h2>
                    <div class="card-tools">
                        <a class="btn btn-primary" href="{{ route('admin.orders.index') }}"> Back</a></i></a>
                    </div>
                </div>

                <div class="card-body ">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group  ml-n2">
                                <label for="category_id"> Category</label>
                                <select class="form-control" id="category">
                                    <option selected value="" disabled>--Select Category Number--</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                    </div>
                </div>
                <div class="row py-3  d-flex" id="category-items" style="  min-height: 50vh; ">

                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        let orderTotal = {{ $order->total }};
        let cartTotal =0;

        $(function() {
            //Getting Items on changing category
            $('#category').on('change', function() {
                let category_id = $(this).val();
                //Clearing previous category items
                clearCategoryItems();
                $.ajax({
                    type: 'GET',
                    url: '{{ route('admin.item.getCategoryItemsData') }}',
                    data: {
                        'category_id': category_id
                    },
                    success: function(data) {
                        if (data.message === 'success') {
                            //Getting food items
                            data.items.forEach(function(item) {
                                $('#category-items').append(template(item.id, item.name,
                                    item.price, item.image));
                            })
                        } else {
                            console.log(data.message);
                        }
                    },
                    error: function(xhr) {
                        console.log('Internal Server Error')
                    }
                });
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
                                sweetAlert('Success', data.message, 'success');
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
            let total = cartTotal + orderTotal;
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
    </script>
@endsection
