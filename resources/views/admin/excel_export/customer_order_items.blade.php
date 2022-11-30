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
        <tr>
            <th>Bill No</th>
            <th>Item</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Sub Total</th>
            <th>Created At</th>
        </tr>
        <tbody>
            @php
                $orders_total = 0;
                $orders_discount = 0;
                $orders_delivery_charge = 0;
                $orders_net_total = 0;
            @endphp
            @foreach ($customer->orders as $order)
                @php
                    $orders_total += $order->total;
                    $orders_discount += $order->discount;
                    $orders_delivery_charge += $order->delivery_charge;

                    $orders_net_total += $order->net_total;

                @endphp
                @foreach ($order->order_items as $order_item)
                    <tr>
                        <th>{{ $order->bill_no }}</th>
                        <th>{{ $order_item->item->name }}</th>
                        <th>{{ $order_item->price }}</th>
                        <th>{{ $order_item->total }}</th>
                        <th>{{ $order_item->total * $order_item->price }}</th>
                        <th>{{ $order_item->created_at }}</th>

                    </tr>
                @endforeach
            @endforeach
        </tbody>

        <tr>
            <th colspan="4">Total</th>
            <th>{{ $orders_total }}</th>
        </tr>
        <tr>
            <th colspan="4">Discount</th>
            <th>{{ $orders_discount }}</th>
        </tr>
        <tr>
            <th colspan="4">Delivery Charge</th>
            <th>{{ $orders_delivery_charge }}</th>
        </tr>
        <tr>
            <th colspan="4">Net Total</th>
            <th>{{ $orders_net_total }}</th>
        </tr>
    </table>
@endforeach
