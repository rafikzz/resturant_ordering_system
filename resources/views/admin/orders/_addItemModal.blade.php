<div class="modal" tabindex="-1" id="orderModal" aria-labelledby="orderModal" aria-hidden="true">
    <div class="modal-dialog modal-xl ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add More Items</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body ">
                    <div class="row">
                        <div class="col-lg-6">
                            @csrf
                            @method('PUT')
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <h3>Order List</h3>
                                        <table class="table " style="  min-height: 20vh; ">
                                            <thead>
                                                <th>Item Name</th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                                <th>Action</th>
                                            </thead>
                                            <tbody id="order-list">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h2 class="card-title">Add Food Item</h2>
                                </div>

                                <div class="card-body ">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group  ml-n2">
                                                <label for="category_id"> Category</label>
                                                <select class="form-control" id="category">
                                                    <option selected value="" disabled>--Select Category Number--
                                                    </option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                                <div class="row py-3  d-flex" id="category-items" style="  min-height: 50vh; ">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
        </form>
    </div>
</div>
</div>
