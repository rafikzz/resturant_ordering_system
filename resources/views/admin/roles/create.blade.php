@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-success ">
                <div class="card-header">
                    <h2 class="card-title">Add Role</h2>
                    <div class="card-tools">
                        <a class="btn btn-primary" href="{{ route('admin.roles.index') }}"> Back</a></i></a>
                    </div>
                </div>
                <div class="card-body ">
                    <form method="POST" action="{{ route('admin.roles.store') }}">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="label" for="name">Role Name @component('compoments.required')
                                    @endcomponent
                                </label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                    class="form-control  @error('name') is-invalid @enderror" minlength="3"
                                    placeholder="Enter Role Name" required>
                                @error('name')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <label class="permissions" for="confirm-password">Permssions  @component('compoments.required')
                            @endcomponent </label>

                        <div class="row">

                            @foreach ($permissions as $permission)
                                <div class="col-3">
                                    <div class="form-check">
                                        <input class="form-check-input" id="{{ 'permission-'.$permission->id }}" name="permissions[]" value="{{ $permission->id }}"
                                            type="checkbox">
                                        <label class="form-check-label" for="{{ 'permission-'.$permission->id }}">{{ $permission->name }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('permissions')
                            <span class=" text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
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
