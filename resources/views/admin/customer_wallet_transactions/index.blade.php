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
                            <th>Order</th>
                            <th>Order Total</th>
                            <th>Paid Amount </th>
                            <th>Operation Amount</th>
                            <th>Current Balance</th>
                            <th>Transaction Type</th>
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
                        data: 'order.bill_no',
                        name: 'order.bill_no',
                        render: function(data){
                            if(data)
                            {
                             return   data;
                            }else{
                             return 'N/A';
                            }
                        }

                    },
                    {
                        data: 'order.net_total',
                        name: 'order.net_total',
                        render: function(data){
                            if(data)
                            {
                             return   data;
                            }else{
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
                        name: 'current_amount'
                    },
                    {
                        data: 'transaction_type.name',
                        name: 'transaction_type.name',
                        render: function(data){
                            if(data)
                            {
                             return   data;
                            }else{
                             return 'N/A';
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
