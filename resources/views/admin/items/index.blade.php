@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-dark">
                <div class="card-header">
                    <h2 class="card-title">Item List</h2>
                    <div class="card-tools form-inline">
                        @can('item_delete')
                            <select id="mode" class="form-control">
                                <option value="0">List</option>
                                <option value="1">Deleted</option>
                            </select>
                        @endcan
                        @can('item_create')
                            <a class="btn btn-success ml-3" href="{{ route('admin.items.create') }}"> <i class="fa fa-plus"></i></a>
                        @endcan
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered" id="table">
                        <thead>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Category</th>
                            <th>Image</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </thead>
                        <tbody id="tablecontents">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
                "aaSorting": [],
                ajax: {
                    url: "{{ route('admin.items.getData') }}",
                    data: function(d) {
                        d.mode = $('#mode').val();
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'status',
                        name: 'status',

                        orderable: false,
                    },
                    {
                        data: 'category',
                        name: 'category',
                        render: function(data) {
                            return '<i class="badge badge-info">' + data + '</i>';
                        },
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'image',
                        name: 'image',
                        render: function(data) {
                            return '<img width="120px" height="100px" src="' + data + '"></img>';
                        },
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render:{
                            _:'display',
                            sort:'timestamp'
                        },
                        searchable: false

                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]

            });

            //for sortable table
            $("#tablecontents").sortable({
                items: "tr",
                cursor: 'move',
                opacity: 0.6,
                update: function() {
                    sendOrderToServer();
                }
            });


            //To change order while changing table
            function sendOrderToServer() {
                var order = [];
                $('tr.row1').each(function(index, element) {
                    order.push({
                        id: $(this).attr('data-id'),
                        position: index + 1
                    });
                });
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('admin.item.updateOrder') }}",
                    data: {
                        order: order,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            console.log(response);
                        } else {
                            console.log(response);
                        }
                    }
                });
            }
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

            //for changing status
            $(document).on('click', '.changeStatus', function(e) {
                e.preventDefault();
                let btn = $(this).closest('[type=checkbox]');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to change item status?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let item_id = btn.data('id');
                        let status = ($(this).is(":checked")) ? 0 : 1;
                        let clickedBtn = btn;
                        $.ajax({
                            type: "GET",
                            url: '{{ route('admin.item.changeStatus') }}',
                            data: {
                                'status': status,
                                'item_id': item_id
                            },
                            success: function(res) {
                                if (res.success) {
                                    btn.parent().find('label').text(res.status).trigger(
                                        'change');
                                    btn.prop('checked', res.checked);
                                } else {
                                    Swal.fire('Something went wrong!');
                                }
                            },
                            error: function() {
                                alert('Internal Server Error !');
                            }
                        });
                    }
                });

            });
        });
    </script>
@endsection
