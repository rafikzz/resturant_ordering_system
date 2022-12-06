@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-dark">
                <div class="card-header">
                    <h2 class="card-title">Patient Discharge Payments</h2>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered" width="100%" id="table">
                        <thead>
                            <th>Id</th>
                            <th>Patient Name</th>
                            <th>Register No</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Discount</th>
                            <th>Created At</th>
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
                    url: "{{ route('admin.patient_discharge_payment.getData') }}",
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'customer.name',
                        name: 'customer.name'
                    },
                    {
                        data: 'customer.patient.register_no',
                        name: 'customer.patient.register_no',
                    }, {
                        data: 'total_amount',
                        name: 'total_amount',

                    },
                    {
                        data: 'paid_amount',
                        name: 'paid_amount',

                    },
                    {
                        data: 'discount',
                        name: 'discount',

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

                ]

            });

        });
    </script>
@endsection
