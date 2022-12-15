@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-success ">
                <div class="card-header">
                    <h2 class="card-title">Add Item</h2>
                    <div class="card-tools">
                        <a class="btn btn-primary" href="{{ route('admin.items.index') }}"> Back</a></i></a>
                    </div>
                </div>
                <div class="card-body ">
                    <form method="POST" action="{{ route('admin.items.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="label" for="name">Item Name</label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                    class="form-control  @error('name') is-invalid @enderror" minlength="3"
                                    placeholder="Enter Name" required autocomplete="off">
                                @error('name')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="label" for="price">Staff Item Price</label>
                                <input type="number" name="price" value="{{ old('price') }}"
                                    class="form-control  @error('price') is-invalid @enderror" min="0" step="0.01"
                                    placeholder="Enter Staff Item Price" required autocomplete="off">
                                @error('price')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="label" for="guest_price">Guest Item Price</label>
                                <input type="number" name="guest_price" value="{{ old('guest_price') }}"
                                    class="form-control  @error('guest_price') is-invalid @enderror" min="0" step="0.01"
                                    placeholder="Enter Guest Item Price" required autocomplete="off">
                                @error('guest_price')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-md-6 ">
                                <label for="category_id"> Category</label>
                                <select class="form-control" name="category_id" required>
                                    <option selected value="" disabled>--Select Category--</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->title }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
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
