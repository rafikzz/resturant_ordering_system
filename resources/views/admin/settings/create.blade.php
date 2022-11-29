@extends('layouts.admin.master')
@section('content')
    <div class="row">
        <div class="col">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h2 class="card-title">Setting</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.settings.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label" for="company_name">Company Name</label>
                                    <input type="text" name="company_name"
                                        value="{{ (old('company_name') ?: isset($setting)) ? $setting->company_name : '' }}"
                                        class="form-control  @error('company_name') is-invalid @enderror" autocomplete="off"
                                        minlength="3" placeholder="Enter Name" required>
                                    @error('company_name')
                                        <span class=" text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label" for="contact_information">Contact Information</label>
                                    <input type="text" name="contact_information"
                                        value="{{ (old('contact_information') ?: isset($setting)) ? $setting->contact_information : '' }}"
                                        class="form-control  @error('contact_information') is-invalid @enderror" autocomplete="off"
                                        minlength="3" placeholder="Enter Contact Information" required>
                                    @error('contact_information')
                                        <span class=" text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label" for="office_location">Office Location</label>
                                    <input type="text" name="office_location"
                                        value="{{ (old('office_location') ?: isset($setting)) ? $setting->office_location : '' }}"
                                        class="form-control  @error('office_location') is-invalid @enderror" minlength="3" autocomplete="off"
                                        placeholder="Enter Office Location" required>
                                    @error('office_location')
                                        <span class=" text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group  d-flex">
                                    <div class="col-md-6 ml-n2">
                                        <label for="exampleInputFile">Upload Logo</label>
                                        <div class="input-group ">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="logo"
                                                    id="image" >
                                                <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <img id="preview-image-before-upload"
                                            src="{{ isset($setting) ? $setting->logo() : asset('noimgavialable.png') }}"
                                            width="150">
                                    </div>
                                </div>
                                @error('image')
                                    <span class=" text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch  ">
                                        <input type="checkbox" value="1" class="custom-control-input" name="service_charge_status"
                                            id="service_charge_status" {{ (old('service_charge_status') ?: isset($setting)) ? $setting->service_charge_status?'checked':'' : '' }}>
                                        <label class="custom-control-label" for="service_charge_status">Enable Service Charge</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label" for="service_charge">Service Charge(%)</label>
                                    <input type="number" name="service_charge"
                                        value="{{ (old('service_charge') ?: isset($setting)) ? $setting->service_charge : '' }}"
                                        class="form-control  @error('service_charge') is-invalid @enderror" autocomplete="off"
                                        minlength="3" placeholder="Enter Service Charge(%)" required>
                                    @error('service_charge')
                                        <span class=" text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6  py-2">
                                <div class="form-group">
                                    <div class="custom-control custom-switch  ">
                                        <input type="checkbox" value="1" class="custom-control-input" name="tax_status"
                                        {{ (old('tax_status') ?: isset($setting)) ? $setting->tax_status?'checked':'' : '' }}
                                            id="tax_status">
                                        <label class="custom-control-label" for="tax_status">Enable Tax</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label" for="tax">Tax(%)</label>
                                    <input type="number" name="tax"
                                        value="{{ (old('tax') ?: isset($setting)) ? $setting->tax : '' }}"
                                        class="form-control  @error('tax') is-invalid @enderror" autocomplete="off"
                                        minlength="3" placeholder="Enter Tax(%)" required>
                                    @error('tax')
                                        <span class=" text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6  py-2">
                                <div class="form-group">
                                    <div class="custom-control custom-switch  ">
                                        <input type="checkbox" value="1" class="custom-control-input" name="delivery_charge_status"
                                        {{ (old('delivery_charge_status') ?: isset($setting)) ? $setting->delivery_charge_status?'checked':'' : '' }}
                                            id="delivery_charge_status">
                                        <label class="custom-control-label" for="delivery_charge_status">Enable Delivery Charge</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label" for="delivery_charge">Delivery Charge</label>
                                    <input type="number" name="delivery_charge"
                                        value="{{ (old('delivery_charge') ?: isset($setting)) ? $setting->delivery_charge : '' }}"
                                        class="form-control  @error('delivery_charge') is-invalid @enderror" autocomplete="off"
                                        minlength="3" placeholder="Enter delivery_charge in rs" required>
                                    @error('delivery_charge')
                                        <span class=" text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary  mt-3">Save Setting</button>
                            </div>
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
