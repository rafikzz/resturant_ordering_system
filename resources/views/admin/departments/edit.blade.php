@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-warning ">
                <div class="card-header">
                    <h2 class="card-title">Add Department</h2>
                    <div class="card-tools">
                        <a class="btn btn-primary" href="{{ route('admin.departments.index') }}"> Back</a></i></a>
                    </div>
                </div>
                <div class="card-body ">
                    <form method="POST" action="{{ route('admin.departments.update', $department->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="label" for="name">Department Name  @component('compoments.required')
                                    @endcomponent</label>
                                <input type="text" name="name" value="{{ old('name') ?: $department->name }}"
                                    class="form-control  @error('name') is-invalid @enderror" minlength="3"
                                    placeholder="Enter Department Name" required autocomplete="off">
                                @error('name')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
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
