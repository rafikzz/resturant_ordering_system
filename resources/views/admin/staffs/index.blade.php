@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-dark">
                <div class="card-header">
                    <h2 class="card-title">Staff List</h2>
                    <div class="card-tools form-inline">
                        @can('staff_create')
                            <a class="btn btn-success ml-3" href="{{ route('admin.staffs.create') }}"> <i
                                    class="fa fa-plus"></i></a>
                        @endcan
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered" width="100%" id="table">
                        <thead>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Phone No.</th>
                            <th>Wallet Balance</th>
                            <th>Status</th>
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
    <script>
        $(document).ready(function() {
            // $('#table').DataTable({
            //     "aaSorting": []
            // });
            var table = $('#table').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                searchDelay: 1000,
                order: [
                    [0, 'desc']
                ],
                ajax: {
                    url: "{{ route('admin.staff.getData') }}",
                    data: function(d) {
                        d.mode = $('#mode').val();
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'phone_no',
                        name: 'phone_no',

                    },
                    {
                        data: 'balance',
                        name: 'balance',
                        render:function(balance) {
                            if( balance < 0) {
                                return -balance +'(due)';
                            }else  {
                                return balance;
                            }
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        searchable: false,
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
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]

            });

            //for delete btn
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                let form = $(this);
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to  delete user?",
                    icon: 'danger',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this)
                            .closest("form").submit();
                    }
                });
            });
              //for changing status
              $(document).on('click', '.changeStatus', function(e) {
                e.preventDefault();
                let btn = $(this).closest('[type=checkbox]');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to change staff status?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let customer_id = btn.data('id');
                        let status = ($(this).is(":checked")) ? 0 : 1;
                        let clickedBtn = btn;
                        $.ajax({
                            type: "GET",
                            url: '{{ route('admin.staff.changeStatus') }}',
                            data: {
                                'status': status,
                                'customer_id': customer_id
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
