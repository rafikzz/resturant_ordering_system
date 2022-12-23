@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-success ">
                <div class="card-header">
                    <h2 class="card-title">Add Status</h2>
                    <div class="card-tools">
                        <a class="btn btn-primary" href="{{ route('admin.statuses.index') }}"> Back</a></i></a>
                    </div>
                </div>
                <div class="card-body ">
                    <form method="POST" action="{{ route('admin.statuses.store') }}">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="label" for="title">Status Name @component('compoments.required')

                                    @endcomponent</label>
                                <input type="text" name="title" value="{{ old('title') }}"
                                    class="form-control  @error('title') is-invalid @enderror" minlength="3" autocomplete="off"
                                    placeholder="Enter title" required>
                                @error('title')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label>Status Color @component('compoments.required')

                                    @endcomponent</label>
                                <div class="input-group my-colorpicker2 colorpicker-element" data-colorpicker-id="2">
                                    <input type="text" name="color" class="form-control" data-original-title=""
                                        title="">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-square"
                                                style="color: rgb(0, 0, 0);"></i></span>
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
                            <button type="submit" name="new" value="1" class="btn btn-primary  mt-3">Save and Create</button>
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
