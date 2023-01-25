<div class="modal fade" id="kot-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="overlay" id="overlay" style="display: none">
                <i class="fas fa-2x fa-sync fa-spin"></i>
            </div>
            <div class="modal-header">
                <h4 class="modal-title">KOT Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12">
                        <b>Bill No</b>: <b id="kot-bill-no"></b>
                    </div>
                    <div class="col-12">
                        <b>Customer Name</b>: <b id="kot-customer-name"></b>
                    </div>
                    <div class="col-12">
                        <b>Customer Contact</b>: <b id="kot-customer-contact"></b>
                    </div>
                    <div class="col-12" id="talbeNO">
                        <b>Table No</b>: <b id="kot-table-no"></b>
                    </div>
                    <div class="col-12" id="orderDate">
                        <b>Order Date</b>: <b id="kot-order-date"></b>
                    </div>
                    <div class="col-12">
                        <b>Status</b>: <b id="kot-order-status"></b>
                    </div>

                </div>
                <div class="row mt-4" id="kot-ordered-tems">

                </div>
            </div>
            <div class="modal-footer justify-content-end">
                <button type="button" class="btn btn-secondary print-this" id="print-kot-all">Print All KOT</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
