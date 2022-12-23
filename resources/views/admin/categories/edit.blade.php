@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-warning ">
                <div class="card-header">
                    <h2 class="card-title ">Edit Menu Category</h2>
                    <div class="card-tools">
                        <a class="btn btn-primary" href="{{ route('admin.categories.index') }}"> Back</a></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.categories.update', $category->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="label" for="title">Category Name @component('compoments.required')
                                    @endcomponent</label>
                                <input type="text" name="title" value="{{ old('title') ?: $category->title }}"
                                    class="form-control  @error('title') is-invalid @enderror" minlength="3"
                                    placeholder="Enter Category Name" required autocomplete="off">
                                @error('title')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="label" for="coupon_discount_percentage">Coupon Discount Percentage @component('compoments.required')
                                    @endcomponent</label>
                                <input type="number" name="coupon_discount_percentage" value="{{ old('coupon_discount_percentage') ?: $category->coupon_discount_percentage }}"
                                    class="form-control  @error('coupon_discount_percentage') is-invalid @enderror" min="0" max="100" step="0.01"
                                    placeholder="Enter Coupon Discount Percentage" required autocomplete="off">
                                    <span class="text-xs">Note: Set this value 0 if you don't want coupon discount to be applied for category of food items</span>

                                @error('coupon_discount_percentage')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="label" for="order">Category Order @component('compoments.required')
                                    @endcomponent</label>
                                <input type="number" min="0" step="1" name="order"
                                    value="{{ old('order') ?: $category->order }}"
                                    class="form-control  @error('order') is-invalid @enderror" placeholder="Enter order"
                                    required>
                                    <span class="text-xs">Note: Order in which you want your category to be listed</span>
                                @error('order')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
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
                            <div class="col-md-6 p-t-2">
                                <img id="preview-image-before-upload" src="{{ $category->image() }}" width="150">
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
