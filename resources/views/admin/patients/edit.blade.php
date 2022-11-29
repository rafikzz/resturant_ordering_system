@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-warning ">
                <div class="card-header">
                    <h2 class="card-title">Edit Patient</h2>
                    <div class="card-tools">
                        <a class="btn btn-primary" href="{{ route('admin.patients.index') }}"> Back</a></i></a>
                    </div>
                </div>
                <div class="card-body ">
                    <form method="POST" action="{{ route('admin.patients.update',$customer->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="label" for="name">Staff Name</label>
                                <input type="text" name="name" value="{{ old('name')?:$customer->name }}"
                                    class="form-control  @error('name') is-invalid @enderror" autocomplete="off"
                                    placeholder="Enter Staff Name" required>
                                @error('name')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="label" for="phone_no">Contact No.</label>
                                <input type="text" name="phone_no" value="{{ old('phone_no')?:$customer->phone_no }}"
                                    class="form-control  @error('phone_no') is-invalid @enderror" autocomplete="off"
                                    placeholder="Enter phone_no" required>
                                @error('phone_no')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="label" for="register_no">Register No.</label>
                                <input type="text" name="register_no" value="{{ old('register_no')?:($customer->patient)?$customer->patient->register_no:'' }}"
                                    class="form-control  @error('register_no') is-invalid @enderror" autocomplete="off"
                                    placeholder="Enter Register No" required>
                                @error('register_no')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary  mt-3">Edit Customer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(function() {
            $('.my-colorpicker2').colorpicker();
            $('.my-colorpicker2').on('colorpickerChange', function(event) {
                $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
            });
        });
    </script>
@endsection
