@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <table class="table table-bordered">
                <tr>
                    <td>Staff Name </td>
                    <td>{{ $customer->name }}</td>
                </tr>
                <tr>
                    @if ($customer->balance < 0)
                        <td>Balance</td>
                        <td>Rs {{ -$customer->balance }}(Due)</td>
                    @else
                        <td>Balance</td>
                        <td>Rs {{ $customer->balance }}(Payable)</td>
                    @endif
                </tr>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-12 ">
            <div class="card card-primary card-outline card-outline-tabs">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="orders" role="tablist">
                        <li class="active nav-item">
                            <a href="#tab-table1" class="nav-link active" data-toggle="tab">Order History</a>
                        </li>
                        <li class="nav-item">
                            <a href="#tab-table2" class="nav-link" data-toggle="tab">Wallet History</a>
                        </li>

                    </ul>
                </div>
                <div class="card-body">
                    <div>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab-table1">
                                <table id="myTable1" class="table table-striped table-bordered" cellspacing="0"
                                    width="100%">
                                    <thead>
                                        <th>Id</th>
                                        <th>Bill No</th>
                                        <th>Discount</th>
                                        <th>Net Total</th>
                                        <th>Created At</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane" id="tab-table2">
                                <table id="myTable2" class="table table-striped table-bordered" cellspacing="0"
                                    width="100%">
                                    <thead>
                                        <th>Id</th>
                                        <th>Order</th>
                                        <th>Order Total</th>
                                        <th>Paid Amount </th>
                                        <th>Operation Amount</th>
                                        <th>Current Balance</th>
                                        <th>Transaction Type</th>
                                        <th>Operation Type</th>
                                        <th>Author</th>
                                        <th>Created At</th>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>

    </div>

    @include('admin.orders._orderDetailModal')
@endsection
@section('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function() {

            var myTable1 = $('#myTable1').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                order: [
                    [4, 'desc']
                ],
                ajax: {
                    url: "{{ route('admin.orders.getData') }}",
                    data: function(d) {
                        d.mode = 'history',
                            d.customer_id = {{ $customer->id }};

                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'bill_no',
                        name: 'bill_no'
                    },
                    {
                        data: 'discount',
                        name: 'discount'
                    },
                    {
                        data: 'net_total',
                        name: 'net_total',

                    },

                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: {
                            _: 'display',
                            sort: 'timestamp'
                        },
                        searchable: false

                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data) {
                            return '<i class="badge text-sm" style="background-color:' + data
                                .color + '">' + data.title + '</i>';
                        },
                        searchable: false,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]

            });


            var myTable2 = $('#myTable2').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                order: [
                    [0, 'desc']
                ],
                ajax: {
                    url: "{{ route('admin.customers.wallet_transactions.getData') }}",
                    data: function(d) {
                        d.mode = 'history',
                            d.customer_id = {{ $customer->id }};
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'order.bill_no',
                        name: 'order.bill_no',
                        render: function(data) {
                            if (data) {
                                return data;
                            } else {
                                return 'N/A';
                            }
                        }

                    },
                    {
                        data: 'order.net_total',
                        name: 'order.net_total',
                        render: function(data) {
                            if (data) {
                                return data;
                            } else {
                                return 'N/A';
                            }
                        }
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount',
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'current_amount',
                        name: 'current_amount',
                        render: function(current_amount) {
                            if (current_amount < 0) {
                                return -current_amount + '(Due)';
                            } else {
                                return current_amount;
                            }
                        }
                    },
                    {
                        data: 'transaction_type.name',
                        name: 'transaction_type.name',
                        render: function(data) {
                            if (data) {
                                return data;
                            } else {
                                return 'N/A';
                            }
                        }
                    },
                    {
                        data: 'transaction_type.is_add',
                        name: 'transaction_type.is_add',
                        searchable: false,
                        render: function(data) {
                            if (data) {
                                return 'Add';
                            } else {
                                return 'Subtract';
                            }
                        }
                    },


                    {
                        data: 'author.name',
                        name: 'author.name',
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: {
                            _: 'display',
                            sort: 'timestamp'
                        },
                        searchable: false

                    }

                ]

            });

            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                $.fn.dataTable.tables({
                    visible: true,
                    api: true
                }).columns.adjust();
            });


            $(document).on('click', '.get-detail', function() {
                let order_id = $(this).attr('rel');
                $('#modal-lg').modal('toggle');
                $('.get-detail').attr('disabled', true);

                clearModal();

                $.ajax({
                    type: 'GET',
                    url: '{{ route('admin.orders.getOrderDetail') }}',
                    data: {
                        'order_id': order_id
                    },
                    beforeSend: function() {
                        $('#overlay').show();
                    },
                    success: function(data) {
                        if (data.status === 'success') {
                            setModalData(data.order);
                            $('#get-bill').attr('href', data.billRoute);

                            data.orderItems.forEach(function(item) {
                                $('#table-items').append(template(item.item.name, item
                                    .total_quantity, parseFloat(item.average_price)));
                            });
                            $('#table-items').append(
                                "<tr><td colspan='3'>Total</td><td>" +
                                foramtValue(data.order.total) + "</td></tr>");
                            if (data.order.discount && data.order.discount != 0 || data.order.status_id ==3) {
                                $('#table-items').append(
                                    "<tr><td colspan='3'>Discount</td><td>" +
                                    foramtValue(data.order.discount) + "</td></tr>");
                            }

                            if (data.order.service_charge && data.order.service_charge != 0 ) {
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

                        $('#overlay').hide();
                        $('.get-detail').attr('disabled', false);
                        console.log('Internal Serve Error');
                    }
                });
            });


        });

        function template(name, total_quantity, price) {

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


        }

        function setModalData(order) {
            $('#bill-no').html(order.bill_no);
            $('#customer-name').html(order.customer.name);
            $('#customer-contact').html(order.customer.phone_no);
            $('#order-date').html(order.order_datetime);
            $('#order-status').html(order.status.title);
            if (order.payment_type_id) {
                $('#paymentType').css('display', 'block');
                $('#payment-type').html(order.payment_type.name);
            }

        }

        function foramtValue(val) {
            return 'Rs. ' + val;
        }
    </script>
@endsection
