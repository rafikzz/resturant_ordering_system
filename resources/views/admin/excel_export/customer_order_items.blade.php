@foreach ($customers as $customer)
    <table>
        <tr>
            <th colspan="6">Patient Order Item</th>
        </tr>
        <tr>
            <th colspan="6">Patient Name:{{ $customer->name }}</th>
        </tr>
        <tr>
            <th colspan="6">Patient Register No:{{ $customer->patient->register_no }}</th>
        </tr>
        @forelse ($customer->orders_summary as $orders_summary)
            <tr>
                <th>Total</th>
                <th>{{ $orders_summary->orders_total }}</th>
            </tr>
            <tr>
                <th>Discount</th>
                <th>{{ $orders_summary->orders_discount }}</th>
            </tr>
            @if ($orders_summary->orders_tax != 0)
                <tr>
                    <th>Tax</th>
                    <th>{{ $orders_summary->orders_tax }}</th>
                </tr>
            @endif
            @if ($orders_summary->orders_service_charge != 0)
                <tr>
                    <th>Service Charge</th>
                    <th>{{ $orders_summary->orders_service_charge }}</th>
                </tr>
            @endif
            @if ($orders_summary->orders_delivery_charge != 0)
                <tr>
                    <th>Packaging Charge</th>
                    <th>{{ $orders_summary->orders_delivery_charge }}</th>
                </tr>
            @endif
            <tr>
                <th>Net Total</th>
                <th>{{ $orders_summary->orders_net_total }}</th>
            </tr>
        @empty
            <tr>
                <th>Total</th>
                <th>0</th>
            </tr>
            <tr>
                <th>Discount</th>
                <th>0</th>
            </tr>
            <tr>
                <th>Net Total</th>
                <th>0</th>
            </tr>
        @endforelse
    </table>
@endforeach
