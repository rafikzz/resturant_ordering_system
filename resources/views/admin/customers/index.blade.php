@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-dark">
                <div class="card-header">
                    <h2 class="card-title">Customer List</h2>
                    <div class="card-tools form-inline">
                        @can('customer_create')
                            <a class="btn btn-success ml-3" href="{{ route('admin.customers.create') }}"> <i
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
                            <th>Order Total</th>
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
                    url: "{{ route('admin.customer.getData') }}",
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
                        data: 'orders_total',
                        name: 'orders_total',
                        searchable: false
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

        });
    </script>
@endsection
