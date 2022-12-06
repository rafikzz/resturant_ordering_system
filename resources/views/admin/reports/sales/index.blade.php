@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-dark">
                <form action="{{ route('admin.reports.exportSales') }}" method="POST">
                    <div class="card-header">
                        @csrf
                        @isset($totalSales)
                            <h2 class="badge bg-orange">Total Sales: Rs.{{ $totalSales }}</h2>
                        @endisset
                        <h2 class="badge bg-orange">Todays Sales: Rs.{{ $todaysSales }}</h2>
                        <button id="export" class="btn btn-success">Export
                            Excel</button>
                        <div class="card-tools form-inline">
                            <div class="form-group">
                                <label>Date range:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="date_range" class="form-control float-right"
                                        id="custom-date-range">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="card-body table-responsive">
                    <table class="table table-bordered" width="100%" id="table">
                        <thead>
                            <th>Id</th>
                            <th>Bill No</th>
                            <th>Discount</th>
                            <th>Total</th>
                            <th>Order Datetime</th>
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
            var start = moment();
            var end = moment();

            function cb(start, end) {
                if (!start.isValid() || !end.isValid()) {
                    $(this).val('')
                }
                $('#custom-date-range span').html(start.format('MMMM D, YYYY') + ' - ' + end.format(
                    'MMMM D, YYYY'));
            }


            $('#custom-date-range').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last 3 Month': [moment().subtract(3, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')],
                    'Last 6 Month': [moment().subtract(6, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                    'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1,
                        'year').endOf('year')],
                }
            }, cb);


            cb(start, end);
            var table = $('#table').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.reports.salesData') }}",
                    data: function(d) {
                        d.startDate = $('#custom-date-range').val().split('-')[0].trim();
                        d.endDate = $('#custom-date-range').val().split('-')[1].trim();
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
                        data: 'total',
                        name: 'total'
                    },
                    {
                        data: 'order_datetime',
                        name: 'order_datetime',
                    },

                ]

            });


            $(document).on('change', '#custom-date-range', function() {
                table.draw();

            });
            // $('#export').on('click', function() {
            //     $.ajax({
            //         url: "{{ route('admin.reports.exportSales') }}",
            //         type: "Get",
            //         data: {
            //             startDate : $('#custom-date-range').val().split('-')[0].trim(),
            //            endDate : $('#custom-date-range').val().split('-')[1].trim(),
            //         },
            //         success: function(data) {
            //             $("#postData").html(data);
            //         },
            //         error: function() {
            //             alert("Form submission failed!");
            //         }
            //     });
            // })



        });
    </script>
@endsection
