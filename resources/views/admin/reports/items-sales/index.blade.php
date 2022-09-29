@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-dark">
                <div class="card-header">
                    {{-- <h2 class="badge bg-orange">Total Sales: Rs.{{ $totalSales }}</h2>
                    <h2 class="badge bg-orange">Todays Sales: Rs.{{ $todaysSales }}</h2> --}}
                    <a href="{{ route('admin.reports.exportItemSales') }}" id="export" class="btn btn-success">Export Excel</a>
                    <div class="card-tools form-inline">
                        <div class="form-group">
                            <label>Date range:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control float-right" id="custom-date-range">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered" id="table">
                        <thead>
                            <th>Id</th>
                            <th>Item Name</th>
                            <th>Total Quantity</th>
                            <th>Total Price</th>
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
            var start = moment();
            var end = moment();

            function cb(start, end) {
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
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            }, cb);

            cb(start, end);
            var table = $('#table').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                "aaSorting":[],
                ajax: {
                    url: "{{ route('admin.reports.itemSalesData') }}",
                    data: function(d) {
                        d.startDate = $('#custom-date-range').val().split('-')[0].trim();
                        d.endDate = $('#custom-date-range').val().split('-')[1].trim();
                    }
                },
                columns: [{
                        data: 'item_id',
                        name: 'item_id',

                    },
                    {
                        data: 'item.name',
                        name: 'item.name',

                    },
                    {
                        data: 'total_quantity',
                        name: 'total_quantity',
                        searchable:false,
                    },
                    {
                        data: 'total_price',
                        name: 'total_price',
                        searchable:false,

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
