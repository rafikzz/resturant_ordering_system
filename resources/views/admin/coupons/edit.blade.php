@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-warning ">
                <div class="card-header">
                    <h2 class="card-title">Add Coupon</h2>
                    <div class="card-tools">
                        <a class="btn btn-primary" href="{{ route('admin.coupons.index') }}"> Back</a></i></a>
                    </div>
                </div>
                <div class="card-body ">
                    <form method="POST" action="{{ route('admin.coupons.update', $coupon->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="label" for="title">Coupon Title</label>
                                <input type="text" name="title" value="{{ old('title') ?: $coupon->title }}"
                                    class="form-control  @error('title') is-invalid @enderror" minlength="3"
                                    placeholder="Enter Title" required autocomplete="off">
                                @error('title')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label class="label" for="discount">Discount Amount</label>
                                <input type="number" min="0" name="discount"
                                    value="{{ old('discount') ?: $coupon->discount }}"
                                    class="form-control  @error('discount') is-invalid @enderror"
                                    placeholder="Enter Discount Amount" required autocomplete="off">
                                @error('discount')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group col-6 ml-n2">
                            <label>Expiry Date:</label>
                            <div class="input-group expiry-date">
                                <input type="text" class="form-control float-right" id="expiry-date" name="expiry_date"
                                    value="{{ old('expiry_date') ?: $coupon->expiry_date }}">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>
                            @error('expiry_date')
                                <span class=" text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary  mt-3">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $('#expiry-date').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 1901,
            locale: {
                format: 'YYYY-M-DD'
            }
        });
    </script>
@endsection
