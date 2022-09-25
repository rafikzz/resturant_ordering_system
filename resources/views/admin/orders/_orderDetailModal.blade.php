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

                <div class="row">
                    <div class="col-12">
                        <b>Bill No</b>: <b id="bill-no"></b>
                    </div>
                    <div class="col-12">
                        <b>Customer Name</b>: <b id="customer-name"></b>
                    </div>
                    <div class="col-12">
                        <b>Customer Contact</b>: <b id="customer-contact"></b>
                    </div>
                    <div class="col-12">
                        <b>Order Date</b>: <b id="order-date"></b>
                    </div>
                    <div class="col-12">
                        <b>Status</b>: <b id="order-status"></b>
                    </div>
                    <div class="col-12" id="paymentType" style="display: hidden">
                        <b>Payment Type</b>: <b id="payment-type"></b>
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
            </div>
            <div class="modal-footer justify-content-end">
                <a href="javascript:void(0)" id="get-bill" class="btn btn-primary" target="_blank">Get Bill</a>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
