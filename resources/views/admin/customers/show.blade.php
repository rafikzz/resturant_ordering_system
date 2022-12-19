@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-dark">
                <div class="card-header">
                    <h2 class="card-title">Customer History</h2>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered" id="table">
                        <thead>
                            <th>Id</th>
                            <th>Bill No</th>
                            <th>Discount</th>
                            <th>Net Total</th>
                            <th>Created At</th>
                            <th>Status</th>
                            <th>Action</th>
                        </thead>
                        <tbody id="tablecontents">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @include('admin.orders._orderDetailModal')
    </div>
@endsection
@section('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function() {
            // $('#table').DataTable({
            //     "aaSorting": []
            // });
            var table = $('#table').DataTable({
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


            $(document).on('change', '#mode', function() {
                table.draw();
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
