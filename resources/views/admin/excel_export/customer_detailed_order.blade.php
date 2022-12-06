<table>
    <tr>
        <th colspan="3">Patient Order Item</th>
    </tr>
    <tr>
        <th colspan="3">Patient Name:{{ $customer->name }}</th>
    </tr>
    <tr>
        <th colspan="3">Patient Register No:{{ $customer->patient->register_no }}</th>
    </tr>
    @if ($orders_item->count())
        <tr>
            <th>Item Name</th>
            <th>Total Quantity</th>
            <th>Total Price</th>
        </tr>
        @foreach ($orders_item as $order_item)
            <tr>
                <th>{{ $order_item->item->name }}</th>
                <th>{{ $order_item->total_quantity }}</th>
                <th>{{ $order_item->total_price }}</th>
            </tr>
        @endforeach
    @else
            <tr>
                <th>No Orders For Patient</th>
            </tr>
    @endif
    @forelse ($customer->orders_summary as $orders_summary)
        <tr>
            <th colspan="2">Total</th>
            <th>{{ $orders_summary->orders_total }}</th>
        </tr>
        <tr>
            <th  colspan="2">Discount</th>
            <th>{{ $orders_summary->orders_discount }}</th>
        </tr>
        @if ($orders_summary->orders_tax != 0)
            <tr>
                <th  colspan="2">Tax</th>
                <th>{{ $orders_summary->orders_tax }}</th>
            </tr>
        @endif
        @if ($orders_summary->orders_service_charge != 0)
            <tr>
                <th  colspan="2">Service Charge</th>
                <th>{{ $orders_summary->orders_service_charge }}</th>
            </tr>
        @endif
        @if ($orders_summary->orders_delivery_charge != 0)
            <tr>
                <th  colspan="2">Packaging Charge</th>
                <th>{{ $orders_summary->orders_delivery_charge }}</th>
            </tr>
        @endif
        <tr>
            <th  colspan="2">Net Total</th>
            <th>{{ $orders_summary->orders_net_total }}</th>
        </tr>
    @empty
        <tr>
            <th colspan="2">Total</th>
            <th>0</th>
        </tr>
        <tr>
            <th colspan="2">Discount</th>
            <th>0</th>
        </tr>
        <tr>
            <th colspan="2">Net Total</th>
            <th>0</th>
        </tr>
    @endforelse
</table>
