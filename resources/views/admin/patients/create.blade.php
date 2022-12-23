@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-success ">
                <div class="card-header">
                    <h2 class="card-title">Add Patient</h2>
                    <div class="card-tools">
                        <a class="btn btn-primary" href="{{ route('admin.patients.index') }}"> Back</a></i></a>
                    </div>
                </div>
                <div class="card-body ">
                    <form method="POST" action="{{ route('admin.patients.store') }}">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="label" for="name">Patient Name  @component('compoments.required')
                                    @endcomponent</label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                    class="form-control  @error('name') is-invalid @enderror" autocomplete="off"
                                    placeholder="Enter Patient Name" required>
                                @error('name')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="label" for="phone_no">Contact No.  @component('compoments.required')
                                    @endcomponent</label>
                                <input type="text" name="phone_no" value="{{ old('phone_no') }}"
                                    class="form-control  @error('phone_no') is-invalid @enderror" autocomplete="off"
                                    placeholder="Enter Contact No" required>
                                @error('phone_no')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="label" for="register_no">Register No.  @component('compoments.required')
                                    @endcomponent</label>
                                <input type="text" name="register_no" value="{{ old('register_no') }}"
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
                            <button type="submit" name="new" value="1" class="btn btn-primary  mt-3">Save and
                                Create</button>
                            <button type="submit" class="btn btn-primary  mt-3">Save and exit</button>
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
