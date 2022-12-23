@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-success ">
                <div class="card-header">
                    <h2 class="card-title">Add Depatment</h2>
                    <div class="card-tools">
                        <a class="btn btn-primary" href="{{ route('admin.departments.index') }}"> Back</a></i></a>
                    </div>
                </div>
                <div class="card-body ">
                    <form method="POST" action="{{ route('admin.departments.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="label" for="name">Depatment Name @component('compoments.required')
                                    @endcomponent</label>
                                <input type="text" name="name" value="{{ old('name') }}"
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

