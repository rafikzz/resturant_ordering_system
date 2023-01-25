<div class="modal fade" id="create" tabindex="-1" aria-labelledby="create" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Choose Customer</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group  ml-n2">
                            <label for="customer_type">Customer Type @component('compoments.required')

                            @endcomponent</label>
                            <select name="customer_type" id="customer_type" class="form-control">
                                @foreach ($customer_types as $customer_type)
                                    <option value="{{ $customer_type->id }}"
                                        {{ $customer_type->id == $default_customer_type_id ? 'selected' : '' }}>
                                        {{ $customer_type->name }}
                                    </option>
                                @endforeach

                            </select>
                            @error('customer_type')
                                <span class=" text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    @component('admin.orders.components._add_customers',
                        ['customers' => $customers, 'customer_type_id' => $default_customer_type_id,'departments'=>$departments,'code_no'=>$code_no ,'customer_id' => null])
                    @endcomponent


                </div>
                <div class="col-lg-12">
                    <a class="btn btn-submit" data-bs-dismiss="modal">Ok</a>
                </div>
            </div>
        </div>
    </div>
</div>
