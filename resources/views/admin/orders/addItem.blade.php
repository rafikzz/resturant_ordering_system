@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <form action="{{ route('admin.orders.addItem', $order->id) }}" id="order-form" method="POST">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group  ml-n2">
                                    <label for="table_no"> Table No</label>
                                    <input type="text" class="form-control" placeholder="Set Table No" id="table_no"
                                        autocomplete="off" name="table_no" value="{{ old('table_no') ?: $order->table_no }}"
                                        required readonly>
                                    @error('table_no')
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
                                            <td>{{ $item->item->name }}</td>
                                            <td>{{ $item->total }}</td>
                                            <td>Rs {{ $item->price }}</td>
                                            <td></td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                <thead>
                                    <th>Order Slip {{ isset($order_no)?$order_no+1: 1 }}</th>
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
                        <hr />
                        <div class="row">
                            <div class="col-12 ">
                                <button id="order-submit" class="btn btn-primary float-right">Add to Order</button>
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
                            $('#order-list').append(tableRowTemplate(data.items[item].id, data.items[item].name, data.items[item].price, data
                                .items[item]
                                .quantity));

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
                                sweetAlert('Success', data.message, 'success');
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
            var total = totalAmount + orderTotal;
            $('#totalAmount').html('Rs. ' + total);
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
    </script>
@endsection
