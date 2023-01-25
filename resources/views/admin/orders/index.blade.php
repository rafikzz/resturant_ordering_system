@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-dark">
                <div class="card-header">
                    <h2 class="card-title">Order List</h2>
                    <div class="card-tools form-inline">
                        <select id="mode" class="form-control ">
                            <option value="all">All</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                        @can('order_create')
                            <a class="btn btn-success ml-3" href="{{ route('admin.orders.create') }}"> <i
                                    class="fa fa-plus"></i></a>
                        @endcan
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-sm" width="100%" id="table">
                        <thead>
                            <th>Id</th>
                            <th>Bill No</th>
                            <th>Customer Name</th>
                            {{-- <th>Destination</th> --}}
                            <th>Order Total</th>
                            <th>Discount</th>
                            <th>Net Total</th>
                            <th>Created At</th>
                            <th>Status</th>
                            <th width="100px">Action</th>
                        </thead>
                        <tbody id="tablecontents">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @include('admin.orders._orderDetailModal')
        @include('admin.kot._orderKotDetailModal')
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
                "pageLength": 25,
                order: [
                    [0, 'desc']
                ],
                ajax: {
                    url: "{{ route('admin.orders.getData') }}",
                    data: function(d) {
                        d.mode = $('#mode').val();
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
                        data: 'customer.name',
                        name: 'customer.name',
                        render: function(data) {
                            if (data) {
                                return data;
                            } else {
                                return 'N/a';
                            }
                        }
                    },
                    // {
                    //     data: 'destination',
                    //     name: 'destination',
                    //     render: {
                    //         _: 'display',
                    //         sort: 'order'
                    //     },
                    // },
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


            $(document).on('change', '#mode', function() {
                table.draw();
            });

            //for delete btn
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                let form = $(this);
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to  delete item?",
                    icon: 'danger',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this).closest("form").submit();
                    }
                });
            });
        });


        //For Kot Display
        $(document).on('click', '.kot-detail', function() {
            let order_id = $(this).attr('rel');
            $('#kot-modal').modal('toggle');
            $('.kot-detail').attr('disabled', true);

            clearKOTModal();

            $.ajax({
                type: 'GET',
                url: '{{ route('admin.kot.getOrderDetail') }}',
                data: {
                    'order_id': order_id
                },
                beforeSend: function() {
                    $('#overlay').show();
                },
                success: function(data) {
                    $('#overlay').hide();
                    $('.kot-detail').attr('disabled', false);

                    if (data.status === 'success') {
                        setKOTModalData(data.order);
                        $('#kot-get-bill').attr('href', data.billRoute);
                        for (let key in data.orderItems) {
                            $('#kot-ordered-tems').append(kotTemplate(data.orderItems[key], key, data
                                .order.id));
                        }

                    } else {
                        console.log('false');
                    }


                },
                error: function(xhr) {

                    $('#overlay').hide();
                    $('.kot-detail').attr('disabled', false);
                    console.log('Internal Serve Error');
                }
            });
        });



        $(document).on('click', '.print-this', function() {
            var order_no = $(this).attr('rel');
            var order_id = $(this).attr('data-order_id');
            if (order_id) {
                $.ajax({
                    type: 'GET',
                    url: '{{ route('invoice.printKot') }}',
                    data: {
                        'order_id': order_id,
                        'order_no': order_no,
                    },
                    beforeSend: function() {
                        $('#overlay').show();
                    },
                    success: function(data) {
                        $('#overlay').hide();
                        $('.kot-detail').attr('disabled', false);

                        if (data.status === 'success') {
                            setKOTModalData(data.order);
                            $('#kot-get-bill').attr('href', data.billRoute);
                            for (let key in data.orderItems) {
                                $('#kot-ordered-tems').append(kotTemplate(data.orderItems[key], key,
                                    data.order.id));
                            }

                        } else {
                            console.log('false');
                        }


                    },
                    error: function(xhr) {

                        $('#overlay').hide();
                        $('.kot-detail').attr('disabled', false);
                        console.log('Internal Serve Error');
                    }
                });
            }


        });
        //For Displaying Order Items
        function kotTemplate(items, key, order_id) {
            let template = '<div class="col-6"><h5>Order Slip ' + key +
                '</h5> </div> <div class="col-6"> <a href="javascript:void(0);" rel="' + key +
                '" data-order_id="' + order_id +
                '" class="btn btn-primary btn-xs print-this float-right ">Print KOT</a> </div> <div class="col-12"> <table class="table table-sm" id="order-list-' +
                key + '"> <thead> <th>Item Name</th> <th>Quantity</th> </thead> <tbody> ';
            items.forEach(function(value) {
                template += '<tr><td><b>' + value.item.name + '</b></td><td><b>' + value.total + '</b></td></tr>';
            });
            template += '</tbody></table></div>';
            return template;

        }
        //For Cleariing Kot Modal
        function clearKOTModal() {
            $('#kot-bill-no').html('');
            $('#kot-customer-name').html('');
            $('#kot-customer-contact').html('');
            $('#kot-order-date').html('');
            $('#kot-order-status').html('');
            $('#kot-ordered-tems').html('');
            $('#kot-get-bill').attr('href', 'javascript:void(0)');
            $('#kot-table-no').html('');
            $('#print-kot-all').attr('data-order_id','');


        }
        //For Setting Data in Kot Modal
        function setKOTModalData(order) {
            $('#kot-bill-no').html(order.bill_no);
            $('#kot-customer-name').html(order.customer.name);
            $('#kot-customer-contact').html(order.customer.phone_no);
            $('#kot-order-date').html(order.order_datetime);
            $('#kot-order-status').html(order.status.title);
            $('#kot-table-no').html(order.table_no);
            $('#print-kot-all').attr('data-order_id',order.id);

        }
    </script>
    @include('admin.orders.components.order_detail')
@endsection
