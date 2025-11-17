<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $sale->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            margin: 15mm;
            size: A4 portrait;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            background: white;
            color: #000;
        }

        .invoice-container {
            width: 100%;
            max-width: 180mm;
            padding: 0;
            margin: 0 auto;
            background: white;
        }

        /* Global Header Styles */
        .global-header {
            border-bottom: 3px solid #3b5b0c;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .global-header table {
            width: 100%;
            border: none;
            border-collapse: collapse;
        }

        .global-header td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }

        .global-header .logo-section {
            width: 70px;
        }

        .global-header .logo-section img {
            max-height: 45px;
            max-width: 70px;
            width: auto;
            height: auto;
            display: block;
        }

        .global-header .company-section {
            text-align: center;
            padding: 0 8px;
        }

        .global-header .company-section h2 {
            font-size: 16pt;
            letter-spacing: 1.2px;
            font-weight: bold;
            margin: 0;
            padding: 0;
            line-height: 1.1;
            white-space: nowrap;
        }

        .global-header .company-section p {
            font-size: 6.5pt;
            color: #3b5b0c;
            font-weight: 600;
            margin: 2px 0 0 0;
            padding: 0;
            line-height: 1.2;
        }

        .global-header .invoice-section {
            width: 90px;
            text-align: right;
        }

        .global-header .invoice-section span {
            display: inline-block;
            font-size: 8.5pt;
            font-weight: bold;
            margin: 0;
            padding: 0;
            line-height: 1.3;
        }

        .global-header .invoice-section .subtitle {
            color: #666;
            font-size: 8pt;
            margin-left: 5px;
        }

        .global-header .separator {
            border: 0;
            border-top: 2px solid #000;
            margin: 6px 0 0 0;
            padding: 0;
        }

        /* Info Section */
        .info-row {
            margin-bottom: 12px;
        }

        .info-row table {
            width: 100%;
            font-size: 8.5pt;
            border: none;
        }

        .info-row td {
            vertical-align: top;
            padding: 3px 5px;
            border: none;
        }

        .customer-info {
            width: 55%;
        }

        .customer-info strong {
            font-size: 9pt;
        }

        .invoice-info {
            width: 45%;
            text-align: right;
        }

        .invoice-info table {
            float: right;
            text-align: left;
            width: auto;
            border: none;
        }

        .invoice-info table td {
            padding: 2px 5px;
            font-size: 8.5pt;
            border: none;
        }

        .invoice-info table td:first-child {
            font-weight: bold;
            padding-right: 8px;
        }

        /* Items Table */
        .items-table {
            margin: 10px 0;
            font-size: 8.5pt;
            width: 100%;
            border-collapse: collapse;
        }

        .items-table th {
            background: #e9ecef;
            padding: 6px 5px;
            border: 1px solid #999;
            font-weight: bold;
            text-align: left;
            font-size: 8pt;
        }

        .items-table td {
            padding: 5px;
            border: 1px solid #999;
            font-size: 8.5pt;
        }

        /* Totals Section */
        .totals-section {
            margin: 12px 0;
            text-align: right;
            clear: both;
        }

        .totals-table {
            float: right;
            width: 45%;
            font-size: 8.5pt;
            border: none;
        }

        .totals-table td {
            padding: 3px 8px;
            border: none;
        }

        .totals-table .total-row td {
            border-top: 2px solid #000;
            font-weight: bold;
            padding-top: 6px;
            font-size: 9pt;
        }

        /* Returned Items */
        .returned-section {
            clear: both;
            margin-top: 15px;
            page-break-inside: avoid;
        }

        .returned-section h4 {
            background: #f8f8f8;
            padding: 4px 6px;
            margin-bottom: 8px;
            font-size: 9.5pt;
            border-left: 3px solid #3b5b0c;
        }

        /* Global Footer */
        .global-footer {
            margin-top: 30px;
            page-break-inside: avoid;
            clear: both;
        }

        .global-footer table {
            width: 100%;
            border: none;
            margin-bottom: 8px;
            border-collapse: collapse;
        }

        .global-footer td {
            text-align: center;
            vertical-align: bottom;
            border: none;
            padding: 3px;
            width: 33.33%;
        }

        .global-footer .signature-line {
            font-size: 8pt;
            font-weight: bold;
            margin: 0 0 3px 0;
            padding: 0;
        }

        .global-footer .signature-label {
            font-size: 8pt;
            font-weight: bold;
            margin: 3px 0;
            padding: 0;
        }

        .global-footer img {
            height: 25px;
            max-width: 60px;
            margin: 4px auto 0;
            display: block;
        }

        .global-footer .info-section {
            border-top: 2px solid #3b5b0c;
            padding-top: 6px;
            margin-top: 6px;
        }

        .global-footer .info-section p {
            text-align: center;
            font-size: 7.5pt;
            margin: 2px 0;
            padding: 0;
            line-height: 1.4;
        }

        .global-footer .info-section .terms {
            font-weight: bold;
            margin-top: 5px;
            font-size: 7pt;
        }

        /* Utility Classes */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .fw-bold {
            font-weight: bold;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        /* Clear floats */
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        /* Page Break Control */
        table {
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }
    </style>
</head>

<body>
    <div class="invoice-container">

        <!-- Global Header -->
        <div class="global-header">
            <table>
                <tr>
                    <td class="logo-section">
                        <img src="{{ public_path('images/USN.png') }}" alt="Logo">
                    </td>
                    <td class="company-section">
                        <h2>USN AUTO PARTS</h2>
                        <p>IMPORTERS & DISTRIBUTORS OF MAHINDRA AND TATA PARTS</p>
                    </td>
                    <td class="invoice-section">
                        <span>MOTOR PARTS</span>
                        <span class="subtitle">INVOICE</span>
                    </td>
                </tr>
            </table>
            <hr class="separator">
        </div>

        <!-- Customer and Invoice Info -->
        <div class="info-row">
            <table>
                <tr>
                    <td class="customer-info">
                        <strong>Customer:</strong><br>
                        {{ $sale->customer->name ?? 'Walk-in Customer' }}<br>
                        @if(isset($sale->customer->address) && $sale->customer->address)
                        {{ $sale->customer->address }}<br>
                        @endif
                        Tel: {{ $sale->customer->phone ?? 'N/A' }}
                    </td>
                    <td class="invoice-info">
                        <table>
                            <tr>
                                <td>Invoice #</td>
                                <td>{{ $sale->invoice_number }}</td>
                            </tr>
                            <tr>
                                <td>Sale ID</td>
                                <td>{{ $sale->sale_id }}</td>
                            </tr>
                            <tr>
                                <td>Date</td>
                                <td>{{ $sale->created_at->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td>Time</td>
                                <td>{{ $sale->created_at->format('H:i') }}</td>
                            </tr>
                            <tr>
                                <td>Payment</td>
                                <td>{{ ucfirst($sale->payment_status) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;" class="text-center">#</th>
                    <th style="width: 15%;">ITEM CODE</th>
                    <th style="width: 40%;">DESCRIPTION</th>
                    <th style="width: 10%;" class="text-center">QTY</th>
                    <th style="width: 15%;" class="text-right">UNIT PRICE</th>
                    <th style="width: 15%;" class="text-right">SUBTOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->product_code }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">Rs.{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">Rs.{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td>Subtotal</td>
                    <td class="text-right">Rs.{{ number_format($sale->subtotal ?? $sale->total_amount, 2) }}</td>
                </tr>
                @if(($sale->discount_amount ?? 0) > 0)
                <tr>
                    <td>Discount</td>
                    <td class="text-right">- Rs.{{ number_format($sale->discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>Grand Total</td>
                    <td class="text-right">Rs.{{ number_format($sale->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td>Paid Amount</td>
                    <td class="text-right">Rs.{{ number_format($sale->paid_amount ?? ($sale->total_amount - $sale->due_amount), 2) }}</td>
                </tr>
                @if($sale->due_amount > 0)
                <tr>
                    <td>Due Amount</td>
                    <td class="text-right">Rs.{{ number_format($sale->due_amount, 2) }}</td>
                </tr>
                @endif
            </table>
        </div>
        <div class="clearfix"></div>

        <!-- Returned Items -->
        @if(isset($sale->returns) && count($sale->returns) > 0)
        @php $returnAmount = 0; @endphp
        <div class="returned-section">
            <h4>RETURNED ITEMS</h4>
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 5%;" class="text-center">#</th>
                        <th style="width: 30%;">PRODUCT</th>
                        <th style="width: 15%;">CODE</th>
                        <th style="width: 15%;" class="text-center">RETURN QTY</th>
                        <th style="width: 17%;" class="text-right">UNIT PRICE</th>
                        <th style="width: 18%;" class="text-right">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->returns as $index => $return)
                    @php $returnAmount += $return->total_amount; @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $return->product->name ?? '-' }}</td>
                        <td>{{ $return->product->code ?? '-' }}</td>
                        <td class="text-center">{{ $return->return_quantity }}</td>
                        <td class="text-right">Rs.{{ number_format($return->selling_price, 2) }}</td>
                        <td class="text-right">Rs.{{ number_format($return->total_amount, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr style="background: #f8f8f8; font-weight: bold;">
                        <td colspan="5" class="text-right" style="padding: 6px;">Return Amount:</td>
                        <td class="text-right" style="padding: 6px;">- Rs.{{ number_format($returnAmount, 2) }}</td>
                    </tr>
                    <tr style="background: #e9ecef; font-weight: bold;">
                        <td colspan="5" class="text-right" style="padding: 6px;">Net Amount:</td>
                        <td class="text-right" style="padding: 6px;">Rs.{{ number_format((($sale->subtotal ?? $sale->total_amount) - ($sale->discount_amount ?? 0) - $returnAmount), 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        <!-- Global Footer -->
        <div class="global-footer">
            <table>
                <tr>
                    <td>
                        <p class="signature-line"><strong>.............................</strong></p>
                        <p class="signature-label"><strong>Checked By</strong></p>
                        <img src="{{ public_path('images/tata.png') }}" alt="Checked By">
                    </td>
                    <td>
                        <p class="signature-line"><strong>.............................</strong></p>
                        <p class="signature-label"><strong>Authorized Officer</strong></p>
                        <img src="{{ public_path('images/USN.png') }}" alt="Authorized">
                    </td>
                    <td>
                        <p class="signature-line"><strong>.............................</strong></p>
                        <p class="signature-label"><strong>Customer Stamp</strong></p>
                        <img src="{{ public_path('images/mahindra.png') }}" alt="Customer">
                    </td>
                </tr>
            </table>
            <div class="info-section">
                <p><strong>ADDRESS:</strong> 103 H, Yatiyanthota Road, Seethawaka, Avissawella</p>
                <p><strong>TEL:</strong> (076) 9085352 | <strong>EMAIL:</strong> autopartsusn@gmail.com</p>
                <p class="terms">Goods return will be accepted within 10 days only. Electrical and body parts non-returnable.</p>
            </div>
        </div>
    </div>
</body>

</html