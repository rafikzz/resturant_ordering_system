<div class="modal fade" id="modal-lg">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="overlay" id="overlay" style="display: none">
                <i class="fas fa-2x fa-sync fa-spin"></i>
            </div>
            <div class="modal-header">
                <h4 class="modal-title">Order Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="row" id="order-details">
                    <div class="col-6">
                        <div class="col-12">
                            <h5>General Information</h5>
                        </div>
                        <div class="col-12">
                            <span>Menu Type</span>: <span id="menu-type"></span>
                        </div>
                        <div class="col-12">
                            <span>Bill No</span>: <span id="bill-no"></span>
                        </div>
                        <div class="col-12">
                            <span>Order Date</span>: <span id="order-date"></span>
                        </div>
                        <div class="col-12" id="order-destination">
                            <span>Destination</span>: <span id="destination"></span>
                        </div>
                        <div class="col-12">
                            <span>Status</span>: <span id="order-status"></span>
                        </div>
                        <div class="col-12" id="paymentType">
                            <span>Payment Type</span>: <span id="payment-type"></span>
                        </div>

                    </div>
                    <div class="col-6">
                        <div class="col-12">
                            <h5>Customer Information</h5>
                        </div>
                        <div class="col-12">
                            <span>Customer Name</span>: <span id="customer-name"></span>
                        </div>
                        <div class="col-12">
                            <span>Customer Type</span>: <span id="customer-type"></span>
                        </div>
                        <div class="col-12" id="department" style="display: none;">
                            <span>Department</span>: <span id="depart"></span>
                        </div>
                        <div class="col-12" id="patient" style="display: none;">
                            <span>Patient Register No:</span>: <span id="register-no"></span>
                        </div>
                        <div class="col-12">
                            <span>Customer Contact</span>: <span id="customer-contact"></span>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <table class="table table-stripped table-sm">
                        <thead>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Price per Item</th>
                            <th>SubTotal</th>
                        </thead>
                        <tbody id="table-items">

                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-12">
                        <span>Note</span>: <span  id="note"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-end">
                <a href="javascript:void(0)" id="get-bill" class="btn btn-primary">Print Bill</a>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
