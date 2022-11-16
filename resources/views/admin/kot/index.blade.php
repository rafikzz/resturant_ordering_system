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
                            <option value="processing">Processing</option>
                            <option value="completed">Complete</option>
                        </select>
                        @can('order_create')
                            <a class="btn btn-success ml-3" href="{{ route('admin.orders.create') }}"> <i
                                    class="fa fa-plus"></i></a>
                        @endcan
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-sm" id="table">
                        <thead>
                            <th>Id</th>
                            <th>Bill No</th>
                            <th>Customer Name</th>
                            <th>Destination</th>
                            <th>Destination No</th>
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
        @include('admin.kot._orderDetailModal')
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
                    [0, 'desc']
                ],
                ajax: {
                    url: "{{ route('admin.kot.getData') }}",
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
                        data: 'customer',
                        name: 'customer',
                        render: function($data) {
                            return $data.name;
                        },

                    },
                    {
                        data: 'destination',
                        name: 'destination',
                    },
                    {
                        data: 'destination_no',
                        name: 'destination_no',
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

            $(document).on('click', '.get-detail', function() {
                let order_id = $(this).attr('rel');
                $('#modal-lg').modal('toggle');
                $('.get-detail').attr('disabled', true);

                clearModal();

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
                        $('.get-detail').attr('disabled', false);

                        if (data.status === 'success') {
                            setModalData(data.order);
                            $('#get-bill').attr('href', data.billRoute);
                            for (let key in data.orderItems) {

                                $('#ordered-tems').append(template(data.orderItems[key],key));
                            }

                        } else {
                            console.log('false');
                        }


                    },
                    error: function(xhr) {

                        $('#overlay').hide();
                        $('.get-detail').attr('disabled', false);
                        console.log('Internal Serve Error');
                    }
                });
            });

        });

        $(document).on('click', '.complete-order-item', function() {
            let btn = $(this);
            let item_id = btn.attr('rel');
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{ route('admin.kot.completeOrderItem') }}",
                data: {
                    item_id: item_id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response.status == "success") {
                        btn.closest('td').html('Completed');
                    } else {
                        console.log(response);
                    }
                }
            });
        });

        $(document).on('click','.print-this',function(){
            let key= $(this).attr('rel');
            $('#talbeNO, #orderDate, #order-list-'+key).printThis({
                header: "<h1>KOT Slip</h1>",
                importCSS: true,
                printDelay: 1000,

            });
        });

        function template(items,key) {
            let template ='<div class="col-6"><h5>Order Slip '+key
                +'</h5> </div> <div class="col-6"> <a href="javascript:void(0);" rel="'+key+'" class="btn btn-primary btn-xs print-this float-right ">Print This</a> </div> <div class="col-12"> <table class="table table-sm" id="order-list-'+
                    key+'"> <thead> <th>Item Name</th> <th>Quantity</th> </thead> <tbody> ';
            items.forEach(function(value) {
                template +='<tr><td>'+value.item.name+'</td><td>'+value.total+'</td></tr>';
            });
            template+='</tbody></table></div>';
            return template;

        }

        function clearModal() {
            $('#bill-no').html('');
            $('#customer-name').html('');
            $('#customer-contact').html('');
            $('#order-date').html('');
            $('#order-status').html('');
            $('#ordered-tems').html('');
            $('#get-bill').attr('href', 'javascript:void(0)');
            $('#table-no').html('');


        }

        function setModalData(order) {
            $('#bill-no').html(order.bill_no);
            $('#customer-name').html(order.customer.name);
            $('#customer-contact').html(order.customer.phone_no);
            $('#order-date').html(order.order_datetime);
            $('#order-status').html(order.status.title);
            $('#table-no').html(order.table_no);


        }


        function foramtValue(val) {
            return 'Rs. ' + val;
        }
    </script>
@endsection
