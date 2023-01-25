<div class="modal fade" id="couponModal" tabindex="-1" aria-labelledby="couponModal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Coupon and Note</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="coupon_id">Coupon Type</label>
                            <select name="coupon_id" id="coupon_id" class="form-control">
                                <option value="">None</option>
                                @foreach ($coupons as $coupon)
                                    <option value="{{ $coupon->id }}" rel="{{ $coupon->discount }}">
                                        {{ $coupon->title }} :Rs
                                        {{ $coupon->discount }}
                                    </option>
                                @endforeach
                            </select>
                            @error('coupon_id')
                                <span class=" text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="discount-amount">Discount</label>
                        <div class="btn-group col-md-12">
                            <input id="discount" value="0" max="0" min="0" step=".01"
                                class="form-control" type="number">
                            <input type="hidden" id="discount-amount" name="discount">
                            <button id="apply-discount" type="button"
                                class="btn btn-primary btn-sm ml-2">Apply</button>
                        </div>

                    </div>
                    <div class="col-md-6">
                        <div class="form-group ">
                            <label for="is_delivery">Packaging</label>
                            <select name="is_delivery" id="is_delivery"
                                class="form-control form-control-sm  float-right">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                            @error('is_delivery')
                                <span class=" text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group col-md-6" id="delivery-charge" style="display:none;">
                        <label for="discount-amount">Packaging Charge Amount</label>
                        <div class="btn-group col-md-12">
                            <input id="delivery" min="0" step=".01" class="form-control"
                                value="{{ $delivery_charge }}" type="number">
                            <input type="hidden" id="delivery_charge" value="{!! $delivery_charge !!}"
                                name="delivery_charge">
                            <button id="apply-charge" type="button" class="btn btn-primary btn-sm ml-2">Apply</button>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="note">Note</label>
                            <textarea class="form-control" name="note" id="note" rows="3"></textarea>
                        </div>
                    </div>

                </div>
                <div class="col-lg-12">
                    <a class="btn btn-submit" data-bs-dismiss="modal">Ok</a>
                </div>
            </div>
        </div>
    </div>
</div>
