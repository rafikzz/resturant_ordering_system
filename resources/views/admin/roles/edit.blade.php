@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-warning ">
                <div class="card-header">
                    <h2 class="card-title ">Edit Role</h2>
                    <div class="card-tools">
                        <a class="btn btn-primary" href="{{ route('admin.roles.index') }}"> Back</a></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.roles.update', $role->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="label" for="name">Role Name</label>
                                <input type="text" name="name" value="{{ old('name') ?: $role->name }}"
                                    class="form-control  @error('name') is-invalid @enderror" minlength="3"
                                    placeholder="Enter Name" required readonly>
                                @error('name')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <label class="permissions" for="confirm-password">Permssions</label>
                        <div class="row">
                            @foreach ($permissions as $permission)
                                <div class="col-3">
                                    <div class="form-check">
                                        <input class="form-check-input" name="permissions[]" value="{{ $permission->id }}"
                                            type="checkbox"
                                            {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ $permission->name }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary  mt-3">Edit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
