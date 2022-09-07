@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-warning ">
                <div class="card-header">
                    <h2 class="card-title ">Edit Setting</h2>
                    <div class="card-tools">
                        <a class="btn btn-primary" href="{{ route('admin.settings.index') }}"> Back</a></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.settings.update', $setting->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="label" for="key">Status Key</label>
                                <input type="text" name="key" value="{{ old('key') ?: $setting->key }}"
                                    class="form-control  @error('key') is-invalid @enderror" minlength="3"
                                    placeholder="Enter key" required>
                                @error('key')
                                    <span class=" text-danger" status="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="label" for="value">Status Value</label>
                                <input type="text" name="value" value="{{ old('value') ?: $setting->value }}"
                                    class="form-control  @error('value') is-invalid @enderror"
                                    placeholder="Enter value" required>
                                @error('value')
                                    <span class=" text-danger" status="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>


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
