@extends('layouts.admin.master')

@section('content')
    <form action="{{ route('admin.reports.exportItemSales') }}" method="POST">
        <div class="row">

            <div class="col-6">
                <div class="form-group">
                    <label for="customer_type">Customer Type</label>
                    <select name="customer_type" id="customer_type" class="form-control">
                        <option value="">None</option>
                        @foreach ($customer_types as $customer_type)
                            <option value="{{ $customer_type->id }}">
                                {{ $customer_type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-6">
                <label for="customer_type">Customer</label>
                <select name="customer_id" class="form-control select2" width="100%" id="customer-select">
                    <option value="">All</option>
                </select>
            </div>
        </div>
        <div class="row">

            <div class="col">
                <div class="card card-outline card-dark">
                    @csrf
                    <div class="card-header">
                        {{-- <h2 class="badge bg-orange">Total Sales: Rs.{{ $totalSales }}</h2>
                    <h2 class="badge bg-orange">Todays Sales: Rs.{{ $todaysSales }}</h2> --}}
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
                                    <input type="text" class="form-control float-right" name="date_range"
                                        id="custom-date-range">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body table-responsive">
                        <table class="table table-bordered" width="100%" id="table">
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
    </form>
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
                        'month').endOf('month')],
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
                "aaSorting": [],
                ajax: {
                    url: "{{ route('admin.reports.itemSalesData') }}",
                    data: function(d) {
                        d.startDate = $('#custom-date-range').val().split('-')[0].trim();
                        d.endDate = $('#custom-date-range').val().split('-')[1].trim();
                        d.customer_id = $('#customer-select').val();

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
                        searchable: false,
                    },
                    {
                        data: 'total_price',
                        name: 'total_price',
                        searchable: false,

                    },


                ]

            });


            $('#customer-select').on('change', function() {
                table.draw();
            });


            $(document).on('change', '#custom-date-range', function() {
                table.draw();

            });
            //Getting Customer Type
            $('#customer_type').on('change', function() {
                let customer_type = parseInt($(this).val());

                $.ajax({
                    type: 'GET',
                    url: '{{ route('admin.customer.getType') }}',
                    data: {
                        'customer_type': customer_type,

                    },
                    success: function(data) {

                        if (data.status === 'success') {
                            $('#customer-select').find('option').not(':first').remove();
                            data.customers.forEach(function(customer) {
                                let register_no = '';
                                let code = '';
                                let department = '';
                                let text = customer.name + '(' + customer.phone_no +
                                    ')';


                                if (customer.patient) {
                                    register_no = " Register No:" + customer.patient
                                        .register_no;
                                    text = code + ' ' + customer.name + '(' + customer
                                        .phone_no +
                                        ')' + register_no;
                                }
                                if (customer.staff) {
                                    code = customer.staff.code;
                                    if (customer.staff) {
                                        code = customer.staff.code;
                                        if (customer.staff.department) {
                                            department = ' Department: ' + customer
                                                .staff.department
                                                .name;

                                        }
                                        text = code + ' ' + customer.name + '(' +
                                            customer
                                            .phone_no +
                                            ') ' + department;
                                    }


                                }

                                let newOption = new Option(text, customer.id, true,
                                    true);
                                $('#customer-select').append(newOption);
                            });
                            $('#customer-select').val(null).trigger('change');

                        } else {
                            console.log(data.message);

                        }
                    },
                    error: function(xhr) {
                        console.log('Internal Sever Error');

                    }
                });

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
