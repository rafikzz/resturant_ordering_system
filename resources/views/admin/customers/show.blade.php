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
                            <th>Table No</th>
                            <th>Discount</th>
                            <th>Total</th>
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
                    [5, 'desc']
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
                        data: 'table_no',
                        name: 'table_no',
                    },
                    {
                        data: 'discount',
                        name: 'discount'
                    },
                    {
                        data: 'total',
                        name: 'total'
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

                            data.orderItems.forEach(function(item) {
                                $('#table-items').append(template(item.item.name, item
                                    .quantity, item.price,item.deleted_at));
                            });
                            $('#table-items').append("<tr><td colspan='4'>Discount</td><td>" +
                                data.order.discount + "</td></tr>");

                            $('#table-items').append("<tr><td colspan='4'>Net Total</td><td>" +
                                data.order.net_total + "</td></tr>");

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

        function template(name, quantity, price, deleted_at) {

            if (deleted_at === null) {

                var status = 'Paid';
                return '<tr><td>' + name + '</td><td>' + quantity + '</td><td>Rs. ' +
                    price + '</td><td>' + status + '</td><td>Rs. ' +
                    price * quantity + '</td><</tr>';

            } else {
                var status = 'Cancelled';
                return '<tr><td><s>' + name + '</s></td><td><s>' + quantity + '</s></td><td><s>Rs. ' +
                    price + '</s></td><td><s>' + status + '</s></td><td><s>Rs. ' +
                    price * quantity + '</s></td><</tr>';
            }

        }

        function clearModal() {
            $('#bill-no').html('');
            $('#customer-name').html('');
            $('#customer-contact').html('');
            $('#order-date').html('');
            $('#order-status').html('');
            $('#table-items').html('');

        }

        function setModalData(order) {
            $('#bill-no').html(order.bill_no);
            $('#customer-name').html(order.customer.name);
            $('#customer-contact').html(order.customer.phone_no);
            $('#order-date').html(order.order_datetime);
            $('#order-status').html(order.status.title);

        }
    </script>
@endsection
