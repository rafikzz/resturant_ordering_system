@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-success ">
                <div class="card-header">
                    <h2 class="card-title">Add User</h2>
                    <div class="card-tools">
                        <a class="btn btn-primary" href="{{ route('admin.users.index') }}"> Back</a></i></a>
                    </div>
                </div>
                <div class="card-body ">
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="label" for="name">User Name</label>
                                <input type="text" name="name" value="{{ old('name') }}"
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
                                <input type="email" name="email" value="{{ old('email') }}"
                                    class="form-control  @error('email') is-invalid @enderror" placeholder="Enter Email" autocomplete="off"
                                    required>
                                @error('email')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="label" for="password">Password</label>
                                <input type="password" name="password"
                                    class="form-control  @error('password') is-invalid @enderror" minlength="8"
                                    placeholder="Enter Password" required>
                                @error('password')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="label" for="confirm-password">Confirm Password</label>
                                <input type="password" name="confirm-password"
                                    class="form-control  @error('confirm-password') is-invalid @enderror" minlength="8"
                                    placeholder="Confirm Password" required>
                                @error('confirm-password')
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
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 text-center">
                            <button type="submit" name="new" value="1" class="btn btn-primary  mt-3">Save and Create</button>
                            <button type="submit" class="btn btn-primary  mt-3">Save and exit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
