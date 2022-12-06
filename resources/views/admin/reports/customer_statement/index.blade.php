@extends('layouts.admin.master')
@section('content')
    <div class="row my-3">
        <div class="col-6">
            <div class="form-group">
                <label for="">Customer</label>
                <select name="customer_id" class="form-control form-control-sm select2" id="customer" required>
                    <option value="">--Please Select Customer--</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">
                            {{ $customer->phone_no }} ({{ $customer->name }})</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label>Date range:</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control float-right"  id="custom-date-range" autocomplete="off">
                </div>
            </div>
        </div>
        <div class="col-6">
            <button class="btn btn-success" id="customer-statement">Go</button>
        </div>
    </div>
    <div class="row ">
        <div class="col">
            <div class="card table-responsive">
                <table class="table table-bordered" width="100%">
                    <tr>
                        <td>Total Orders</td>
                        <td>0</td>
                    </tr>
                    <tr>
                        <td>Total Purchase</td>
                        <td>0</td>
                    </tr>
                    <tr>
                        <td>Total Due</td>
                        <td>0</td>
                    </tr>
                    <tr>
                        <td>Total Balance</td>
                        <td>0</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('js')
<script>
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
                    'Last 3 Months':[moment().subtract(90, 'days'), moment()]
                }
            }, cb);

            cb(start, end);

            $('#customer-statement').click(function() {
               let customer_id= $('#customer').val();
               let startDate = $('#custom-date-range').val().split('-')[0].trim();
               let endDate = $('#custom-date-range').val().split('-')[1].trim();
               $.ajax({


               });
            });
</script>
@endsection
