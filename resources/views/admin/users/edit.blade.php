@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-warning ">
                <div class="card-header">
                    <h2 class="card-title ">Edit User</h2>
                    <div class="card-tools">
                        <a class="btn btn-primary" href="{{ route('admin.users.index') }}"> Back</a></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="label" for="name">User Name</label>
                                <input type="text" name="name" value="{{ old('name') ?: $user->name }}"
                                    class="form-control  @error('name') is-invalid @enderror" minlength="3" autocomplete="off"
                                    placeholder="Enter Name" required>
                                @error('name')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="label" for="email">User Email</label>
                                <input type="email" name="email" value="{{ old('email') ?: $user->email }}"
                                    class="form-control  @error('email') is-invalid @enderror" placeholder="Enter Email" autocomplete="off"
                                    required>
                                @error('email')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-md-6 ml-n2">
                            <label class="roles" for="confirm-password">Roles</label>
                            <select class="select2" name="roles[]" multiple="multiple" style="width: 100%;color:black">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"{{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                        {{ $role->name }}</option>
                                @endforeach
                            </select>
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
