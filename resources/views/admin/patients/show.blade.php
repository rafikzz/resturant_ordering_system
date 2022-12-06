@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <table class="table table-bordered">
                <tr>
                    <td>Patient Name </td>
                    <td>{{ $customer->name }}</td>
                </tr>
                @isset($customer_orders_total)
                <tr>
                    <td>Order Total</td>
                    <td>Rs {{ $customer_orders_total }}</td>
                </tr>
                @endisset
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
                            <a href="#tab-table2" class="nav-link" data-toggle="tab">Ordered Items</a>
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
                                        <th>Bill No</th>
                                        <th>Order Total</th>
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
                                        <th>Bill No</th>
                                        <th>Item Name</th>
                                        <th>Total Quantity</th>
                                        <th>Price</th>
                                        <th>Total Price</th>
                                        <th>Ordered Date</th>
                                    </thead>
                                    <tbody id="tablecontents">

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
                columns: [
                    {
                        data: 'bill_no',
                        name: 'bill_no'
                    },
                    {
                        data: 'total',
                        name: 'total'
                    },
                    {
                        data: 'discount',
                        name: 'discount'
                    },
                    {
                        data: 'net_total',
                        name: 'net_total'
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
                "aaSorting":[],
                ajax: {
                    url: "{{ route('admin.patient.getOrderItemData') }}",
                    data: function(d) {
                        d.customer_id = {{ $customer->id }};
                    }
                },
                columns: [{
                        data: 'order.bill_no',
                        name: 'order.bill_no',

                    },
                    {
                        data: 'item.name',
                        name: 'item.name',

                    },
                    {
                        data: 'total',
                        name: 'total',
                        searchable:false,
                    },
                    {
                        data: 'price',
                        name: 'price',
                        searchable:false,

                    },
                    {
                        data: 'total_price',
                        name: 'total_price',
                        searchable:false,

                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        searchable: false

                    },


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
                            if (data.order.discount && data.order.discount !=  0 || data.order.status_id ==3) {
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
