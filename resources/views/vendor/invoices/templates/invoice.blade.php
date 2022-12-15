<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ $invoice->name }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />

    <style type="text/css" media="screen">
        html {
            font-family: sans-serif;
            line-height: 1.15;
            margin: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            text-align: left;
            background-color: #fff;
            font-size: 10px;
            margin: 36pt;
        }

        h4 {
            margin-top: 0;
            margin-bottom: 0.5rem;
        }

        p {
            margin-top: 0;
            margin-bottom: 1rem;
        }

        strong {
            font-weight: bolder;
        }

        img {
            vertical-align: middle;
            border-style: none;
        }

        table {
            border-collapse: collapse;
        }

        th {
            text-align: inherit;
        }

        h4,
        .h4 {
            margin-bottom: 0.5rem;
            font-weight: 500;
            line-height: 1.2;
        }

        h4,
        .h4 {
            font-size: 1.5rem;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody+tbody {
            border-top: 2px solid #dee2e6;
        }

        .mt-5 {
            margin-top: 3rem !important;
        }

        .pr-0,
        .px-0 {
            padding-right: 0 !important;
        }

        .pl-0,
        .px-0 {
            padding-left: 0 !important;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .text-uppercase {
            text-transform: uppercase !important;
        }

        * {
            font-family: "DejaVu Sans";
        }

        hr {
            border: .5px dashed;
        }

        body,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        table,
        th,
        tr,
        td,
        p,
        div {
            line-height: 1.1;
        }

        .party-header {
            font-size: 1.5rem;
            font-weight: 400;
        }


        .border-0 {
            border: none !important;
        }

        .ticket {
            width: 260px;
            max-width: 260px;
        }

        td.description,
        th.description {
            width: 75px;
            max-width: 75px;
        }

        td.quantity,
        th.quantity {
            width: 20px;
            max-width: 20px;
            word-break: break-all;
        }

        td.rate,
        th.rate {
            width: 35px;
            max-width: 35px;
            word-break: break-all;
        }

        td.price,
        th.price {
            width: 60px;
            max-width: 60px;
            word-break: break-all;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="ticket">

        <div class="text-center">
            @if ($invoice->seller->name)
                <p class="seller-name">
                    <strong>{{ $invoice->seller->name }}</strong>
                    <br />
                    @if ($invoice->seller->address)
                        {{ $invoice->seller->address }}
                    @endif
                </p>
                <br>
            @endif
        </div>
        <br />
        <table>
            <tr>
                <td>{{ __('invoices::invoice.serial') }}</td>
                <td>: {{ $invoice->getSerialNumber() }}</td>
            </tr>
            <tr>
                <td> {{ __('invoices::invoice.date') }}</td>
                <td>: {{ $invoice->getDate() }}</td>
            </tr>
            <tr>
                <td>{{ __('invoices::invoice.buyer') }}</td>
                <td>: @if ($invoice->buyer->name)
                        {{ $invoice->buyer->name }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>{{ __('invoices::invoice.phone') }}</td>
                <td>: @if ($invoice->buyer->phone)
                        {{ $invoice->buyer->phone }}
                    @endif
                </td>
            </tr>
        </table>



        {{-- Table --}}
        <table class="table">
            <thead>
                <tr>
                    <th scope="col" class=" pl-0">{{ __('invoices::invoice.particulars') }}</th>
                    @if ($invoice->hasItemUnits)
                        <th scope="col" class="text-center ">{{ __('invoices::invoice.units') }}</th>
                    @endif
                    <th scope="col" class="text-center ">{{ __('invoices::invoice.quantity') }}</th>
                    <th scope="col" class="text-right ">{{ __('invoices::invoice.rate') }}</th>
                    @if ($invoice->hasItemDiscount)
                        <th scope="col" class="text-right ">{{ __('invoices::invoice.discount') }}</th>
                    @endif
                    @if ($invoice->hasItemTax)
                        <th scope="col" class="text-right ">{{ __('invoices::invoice.tax') }}</th>
                    @endif
                    <th scope="col" class="text-right  pr-0">{{ __('invoices::invoice.amount') }}</th>
                </tr>
            </thead>
            <tbody>
                {{-- Items --}}
                @foreach ($invoice->items as $item)
                    <tr>
                        <td class="pl-0 border-0" >{{ $item->title }}</td>
                        <td class="text-center pr-0 quantity border-0">{{ $item->quantity }}</td>
                        <td class="text-right pr-0 rate border-0">
                            {{ floatval($item->price_per_unit) }}
                        </td>
                        <td class="text-right pr-0 price border-0">
                            {{ floatval($item->sub_total_price) }}
                        </td>
                    </tr>
                @endforeach
                {{-- Summary --}}

                @if ($invoice->total_discount)
                    <tr>
                        <td colspan="{{ $invoice->table_columns - 3 }}" class="border-0"></td>
                        <td class="text-right pl-0" colspan="2">{{ __('invoices::invoice.discount') }}</td>
                        <td class="text-right pr-0">
                            {{ $invoice->formatCurrency(floatval($invoice->total_discount)) }}
                        </td>

                    </tr>
                @endif

                @if ($invoice->taxable_amount)
                    <tr>
                        <td colspan="{{ $invoice->table_columns - 3 }}" class="border-0"></td>
                        <td class="text-right pl-0" colspan="2">{{ __('invoices::invoice.taxable_amount') }}</td>
                        <td class="text-right pr-0">
                            {{ $invoice->formatCurrency(floatval($invoice->taxable_amount)) }}
                        </td>
                    </tr>
                @endif
                @if ($invoice->service_charge)
                    <tr>
                        <td colspan="{{ $invoice->table_columns - 3 }}" class="border-0"></td>
                        <td class="text-right pl-0" colspan="2">Service Charge</td>
                        <td class="text-right pr-0">
                            {{ $invoice->formatCurrency(floatval($invoice->service_charge)) }}
                        </td>
                    </tr>
                @endif

                @if ($invoice->total_taxes)
                    <tr>
                        <td colspan="{{ $invoice->table_columns - 3 }}" class="border-0"></td>
                        <td class="text-right pl-0" colspan="2">{{ __('invoices::invoice.tax') }}</td>
                        <td class="text-right pr-0">
                            {{ $invoice->formatCurrency(floatval($invoice->total_taxes)) }}
                        </td>
                    </tr>
                @endif
                @if ($invoice->delivery_charge)
                    <tr>
                        <td colspan="{{ $invoice->table_columns - 3 }}" class="border-0"></td>
                        <td class="text-right pl-0" colspan="2">{{ __('invoices::invoice.delivery_charge') }}</td>
                        <td class="text-right pr-0">
                            {{ $invoice->formatCurrency(floatval($invoice->delivery_charge)) }}
                        </td>
                    </tr>
                @endif
                <tr>
                    <td colspan="{{ $invoice->table_columns - 3 }}" class="border-0"></td>
                    <td class="text-right pl-0" colspan="2">{{ __('invoices::invoice.total_amount') }}</td>
                    <td class="text-right pr-0 ">
                        {{ $invoice->formatCurrency(floatval($invoice->total_amount)) }}
                    </td>
                </tr>
            </tbody>
        </table>
        <hr>
        <p>
            {{ trans('invoices::invoice.thanks_for_visiting') }}
        </p>
        <hr>
        <table>
            @if ($invoice->cashier)
                <tr>
                    <td>{{ __('invoices::invoice.cashier') }}</td>
                    <td>: {{ $invoice->cashier->name }}</td>
                </tr>
            @endif
            @if ($invoice->cashier)
                <tr>
                    <td> {{ __('invoices::invoice.time') }}</td>
                    <td>: {{ $invoice->time }}</td>
                </tr>
            @endif
        </table>

        @if ($invoice->notes)
            <p>
                {{ trans('invoices::invoice.notes') }}: {!! $invoice->notes !!}
            </p>
        @endif
    </div>


    <script type="text/php">
            if (isset($pdf) && $PAGE_COUNT > 1) {
                $text = "Page {PAGE_NUM} / {PAGE_COUNT}";
                $size = 10;
                $font = $fontMetrics->getFont("Verdana");
                $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
                $x = ($pdf->get_width() - $width);
                $y = $pdf->get_height() - 35;
                $pdf->page_text($x, $y, $text, $font, $size);
            }
        </script>
</body>

</html>
