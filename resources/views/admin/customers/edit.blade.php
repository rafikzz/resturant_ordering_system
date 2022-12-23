@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-success ">
                <div class="card-header">
                    <h2 class="card-title">Edit Customer</h2>
                    <div class="card-tools">
                        <a class="btn btn-primary" href="{{ route('admin.customers.index') }}"> Back</a></i></a>
                    </div>
                </div>
                <div class="card-body ">
                    <form method="POST" action="{{ route('admin.customers.update',$customer->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="label" for="name">Customer Name @component('compoments.required')
                                    @endcomponent</label>
                                <input type="text" name="name" value="{{ old('name')?:$customer->name }}"
                                    class="form-control  @error('name') is-invalid @enderror" autocomplete="off"
                                    placeholder="Enter Customer Name" required>
                                @error('name')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="label" for="phone_no">Contact No. @component('compoments.required')
                                    @endcomponent</label>
                                <input type="text" name="phone_no" value="{{ old('phone_no')?:$customer->phone_no }}"
                                    class="form-control  @error('phone_no') is-invalid @enderror" autocomplete="off"
                                    placeholder="Enter Customer Number" required>
                                @error('phone_no')
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
