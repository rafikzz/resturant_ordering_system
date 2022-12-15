@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-success ">
                <div class="card-header">
                    <h2 class="card-title">Add Category</h2>
                    <div class="card-tools">
                        <a class="btn btn-primary" href="{{ route('admin.categories.index') }}"> Back</a></i></a>
                    </div>
                </div>
                <div class="card-body ">
                    <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="label" for="title">Category Name</label>
                                <input type="text" name="title" value="{{ old('title') }}"
                                    class="form-control  @error('title') is-invalid @enderror" minlength="3"
                                    placeholder="Enter Name" required autocomplete="off">
                                @error('title')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="label" for="coupon_discount_percentage">Coupon Discount Percentage</label>
                                <input type="number" name="coupon_discount_percentage" value="{{ old('coupon_discount_percentage') }}"
                                    class="form-control  @error('coupon_discount_percentage') is-invalid @enderror" step="0.01" min="0" max="100"
                                    placeholder="Enter Percentage" required autocomplete="off">
                                @error('coupon_discount_percentage')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="label" for="title">Status</label>
                                <div class="custom-control custom-switch  ">
                                    <input type="checkbox" value="1" class="custom-control-input" name="status"
                                        id="status">
                                    <label class="custom-control-label" for="status">Active</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ml-n2">
                            <div class="col-md-6">
                                <label for="exampleInputFile">Upload Image</label>
                                <div class="input-group ">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="image" id="image">
                                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                    </div>
                                    @error('image')
                                        <span class=" text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6  p-t-2">
                                <img id="preview-image-before-upload" src="{{ asset('noimgavialable.png') }}"
                                    width="150">
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
    <script type="text/javascript">
        $(document).ready(function(e) {
            bsCustomFileInput.init();

            $('#image').change(function() {

                let reader = new FileReader();

                reader.onload = (e) => {

                    $('#preview-image-before-upload').attr('src', e.target.result);
                }

                reader.readAsDataURL(this.files[0]);

            });

        });
    </script>
@endsection
