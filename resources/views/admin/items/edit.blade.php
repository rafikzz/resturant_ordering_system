@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-warning ">
                <div class="card-header">
                    <h2 class="card-title ">Edit Item</h2>
                    <div class="card-tools">
                        <a class="btn btn-primary" href="{{ route('admin.items.index') }}"> Back</a></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.items.update', $item->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="label" for="title">Food Item Name  @component('compoments.required')
                                    @endcomponent</label>
                                <input type="text" name="name" value="{{ old('name') ?: $item->name }}"
                                    class="form-control  @error('name') is-invalid @enderror" minlength="3"
                                    placeholder="Enter Food Item Name" required autocomplete="off">
                                @error('name')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="label" for="price">Staff Item Price  @component('compoments.required')

                                    @endcomponent</label>
                                <input type="number" name="price" value="{{ old('price') ?: $item->price }}"
                                    class="form-control  @error('price') is-invalid @enderror" min="0" step="0.01"
                                    placeholder="Enter Price For Staff" required autocomplete="off">
                                @error('price')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="label" for="guest_price">Guest Item Price  @component('compoments.required')

                                    @endcomponent</label>
                                <input type="number" name="guest_price" value="{{ old('guest_price', $item->guest_price) }}"
                                    class="form-control  @error('guest_price') is-invalid @enderror" min="0"
                                    step="0.01" placeholder="Enter Price For Guest" required autocomplete="off">
                                @error('guest_price')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="category_id"> Category  @component('compoments.required')

                                    @endcomponent</label>
                                <select class="form-control" name="category_id" required>
                                    <option selected value="" disabled>--Select Category--</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $category->id == $item->category_id ? 'selected' : '' }}>
                                            {{ $category->title }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
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
                                <img id="preview-image-before-upload" src="{{ $item->image() }}" width="150">
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
