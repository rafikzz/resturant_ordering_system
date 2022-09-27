@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-dark">
                <div class="card-header">
                    <h2 class="card-title">Wallet Transaction History: {{ $customer->name }}</h2>
                    <div class="card-tools form-inline">
                        @can('customer_create')
                            <a class="btn btn-success ml-3" href="{{ route('admin.customers.wallet_transactions.create',$customer->id) }}"> <i
                                    class="fa fa-plus"></i></a>
                        @endcan
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered" id="table">
                        <thead>
                            <th>Id</th>
                            <th>Previous Amount</th>
                            <th>Amount </th>
                            <th>Current Amount</th>
                            <th>Transaction Type</th>
                            <th>Order</th>
                            <th>Author</th>
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
                        data: 'previous_amount',
                        name: 'previous_amount'
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                    },
                    {
                        data: 'current_amount',
                        name: 'current_amount'
                    },
                    {
                        data: 'transaction_type',
                        name: 'transaction_type',
                        render: function(transaction_type){
                            return transaction_type.name;
                        }
                    },
                    {
                        data: 'order',
                        name: 'order',
                        // render: function(order){

                        //     return order.bill_no;
                        // }
                    },
                    {
                        data: 'author',
                        name: 'author',
                        render: function(author){
                            return author.name;
                        }
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
                        searchable: false,
                        orderable: false

                    }

                ]

            });

        });
    </script>
@endsection
