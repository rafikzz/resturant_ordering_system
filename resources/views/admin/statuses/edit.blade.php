@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-warning ">
                <div class="card-header">
                    <h2 class="card-title ">Edit Status</h2>
                    <div class="card-tools">
                        <a class="btn btn-primary" href="{{ route('admin.statuses.index') }}"> Back</a></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.statuses.update', $status->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="label" for="name">Status Name</label>
                                <input type="text" name="title" value="{{ old('title') ?: $status->title }}" autocomplete="off"
                                    class="form-control  @error('title') is-invalid @enderror" minlength="3"
                                    placeholder="Enter title" required>
                                @error('title')
                                    <span class=" text-danger" status="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label>Status Color</label>
                                <div class="input-group my-colorpicker2 colorpicker-element" data-colorpicker-id="2">
                                    <input type="text" class="form-control" name="color"
                                        value="{{ old('color') ?: $status->color }}" data-original-title="" title="">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-square"
                                                style="color: {{ old('color') ?: $status->color }};"></i></span>
                                    </div>
                                    @error('color')
                                        <span class=" text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

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
