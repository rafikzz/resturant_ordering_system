@extends('layouts.admin.master')
@section('content')
    <div class="row">
        <div class="col">
            <div class="card  card-outline card-success">
                <form action="{{ route('admin.patients.discharge', $customer->id) }}" id="checkout-form" method="POST">
                    @csrf
                    <div class="card-header">
                        <h5 class="card-title">Patient Discharge</h5>
                        <div class="card-tools">
                            <a class="btn btn-primary" href="{{ route('admin.items.index') }}"> Back</a></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        @csrf

                        <div class="row col-6">
                            <div class="col-12">
                                @isset($customer)
                                    <p>Patient Name: {{ $customer->name }}</p>
                                    @if ($customer->patient)
                                        <p>Patient Registration No: {{ $customer->patient->register_no }}</p>
                                    @endif
                                @endisset
                            </div>
                            <div class="form-group col-12">
                                <label class="label" for="order_total">Order Total</label>
                                <input type="number" name="order_total" value="{{ $customer_orders_total }}"
                                    class="form-control  @error('order_total') is-invalid @enderror" autocomplete="off"
                                    placeholder="Enter Total Order Total By Hospital" readonly>
                                @error('order_total')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-6 ">
                                <label class="label" for="discount_type">Discount Type</label>
                                <select name="discount_type" class="form-control" id="discount_type">
                                    <option value="0">Cash</option>
                                    <option value="1">Percentage</option>
                                </select>
                                @error('discount_type')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-6 ">
                                <label class="label" for="discount">Discount Amount</label>
                                <div class="btn-group">
                                    <input type="number" value="{{ old('discount') ?: 0 }}" min="0" id="discount"
                                        max="{{ old('discount_type') == 1 ? 100 : $customer_orders_total }}"
                                        class="form-control  @error('discount') is-invalid @enderror" autocomplete="off"
                                        step="0.01" placeholder="Discount" required id="discount_type">
                                    <button id="apply-discount" type="button"
                                        class="btn btn-primary btn-sm ml-2">Apply</button>
                                </div>
                                <input type="hidden" id="discount_amount" name="discount_amount" value="0">
                                @error('discount')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label class="label" for="paid_amount"> Amount Paid</label>
                                <input type="text" id="paid_amount" name="paid_amount"
                                    value="{{ $customer_orders_total }}"
                                    class="form-control  @error('paid_amount') is-invalid @enderror" autocomplete="off"
                                    placeholder="Enter Total Paid Amount By Hospital" required readonly>
                                @error('paid_amount')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        let order_total = {!! $customer_orders_total !!};
        $('#discount_type').on('change', function() {
            let discount_type = $(this).val();
            if (discount_type == 1) {
                $('#discount').attr('max', '100');

            } else {
                $('#discount').attr('max', order_total);
            }
            $('#discount').val(0);
        });
        $('#apply-discount').on('click', function() {
            let discount_type = $('#discount_type').val();
            let discount = parseFloat($('#discount').val());
            if (discount ||discount == 0) {
                if (discount_type == 1) {
                    if ($('#discount')[0].checkValidity()) {
                        let discount_amount = order_total * ((discount) / 100);
                        let paid_amount = order_total - discount_amount;
                        if (discount<=100) {
                            $('#discount_amount').val(discount_amount);
                            $('#paid_amount').val(paid_amount);
                        }else{
                            alert('Discount Cannot Be greater than Order Total');
                        }


                    } else {

                        $("#discount")[0].reportValidity();
                    }


                } else {
                    if (discount <= order_total) {
                        let paid_amount = order_total - discount;
                        $('#discount_amount').val(discount);
                        $('#paid_amount').val(paid_amount);
                    } else {
                        alert('Discount Cannot Be greater than Order Total');
                    }
                }
            } else {
                alert('Enter Valid Discount');
            }
        });
    </script>
@endsection
