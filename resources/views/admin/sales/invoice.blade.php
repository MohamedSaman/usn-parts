<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $sale->invoice_number }}</title>
    <style>
        @page {
            margin: 20mm; /* Add some default page margins */
        }

        body {
            font-family: "DejaVu Sans", "Segoe UI", Arial, sans-serif;
            font-size: 11px; /* Smaller base font */
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
            background: white;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }

        /* --- HEADER STYLE --- */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            color: #000;
            font-size: 24px;
            font-weight: bold;
        }

        .header p {
            margin: 2px 0;
            font-size: 11px;
        }
        
        .header .invoice-title { /* Changed from quotation-title */
            font-size: 18px;
            font-weight: bold;
            margin-top: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #057642; /* Kept green line */
        }
        /* --- END HEADER STYLE --- */
        

        /* --- INFO SECTION STYLE --- */
        .customer-info {
            margin-bottom: 20px;
        }
        
        .customer-info strong {
            font-weight: bold;
        }

        .invoice-details { /* Changed from quotation-details */
             margin-bottom: 25px;
        }
        
        .details-table {
            border-collapse: collapse;
            width: auto; /* Shrink to content */
        }
        
        .details-table td {
            padding: 2px 8px 2px 0;
            vertical-align: top;
        }
        
        .details-table td:first-child {
            font-weight: bold;
            text-align: left;
        }
        /* --- END INFO SECTION STYLE --- */


        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 11px;
        }

        table.items-table th {
            background: #057642;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
        }

        table.items-table td {
            padding: 8px 6px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        tr.items-row:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }
        
        .amount {
            font-family: "Courier New", monospace;
        }

        /* --- TOTALS STYLE --- */
        .totals-section {
            margin-top: 20px;
            float: right;
            width: 300px; /* Wider for more fields */
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .totals-table td {
            padding: 5px 0;
        }
        
        .totals-table td:first-child {
            font-weight: bold;
            text-align: right;
            padding-right: 15px;
        }
        
        .totals-table td:last-child {
            text-align: right;
            width: 120px;
        }
        
        .totals-table tr.grand-total td {
            border-top: 2px solid #333;
            font-size: 13px;
            font-weight: bold;
            padding-top: 8px;
        }
        
        .totals-table tr.balance-due td {
             font-weight: bold;
        }
        /* --- END TOTALS STYLE --- */

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .notes-section {
            clear: both;
            padding-top: 30px;
            font-size: 11px;
        }
        
        /* --- SIGNATURES STYLE --- (REMOVED) --- */
        /* --- END SIGNATURES STYLE --- (REMOVED) --- */

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }

        @media print {
            body {
                padding: 0;
                margin: 0;
                font-size: 10px;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .invoice-container {
                border: none;
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        
        <div class="header">
            <h2>USN AUTO PARTS</h2>
            <p>103 H, Yatiyanthota Road, Seethawaka, Avissawella</p>
            <p>Phone: (076) 9085252 | Email: autopartsusn@gmail.com</p>
            <div class="invoice-title">SALES INVOICE</div>
        </div>

        <div class="customer-info">
            <strong>Bill To:</strong><br>
            <strong>{{ $sale->customer->name }}</strong><br>
            @if($sale->customer->address)
                {{ $sale->customer->address }}<br>
            @endif
            Tel: {{ $sale->customer->phone }}<br>
            @if($sale->customer->email)
                Email: {{ $sale->customer->email }}
            @endif
        </div>
        
        <div class="invoice-details">
            <table class="details-table">
                <tr>
                    <td><strong>Invoice No:</strong></td>
                    <td>{{ $sale->invoice_number }}</td>
                </tr>
                <tr>
                    <td><strong>Sale ID:</strong></td>
                    <td>{{ $sale->sale_id }}</td>
                </tr>
                <tr>
                    <td><strong>Date:</strong></td>
                    <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            </table>
        </div>
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%" class="text-center">#</th>
                    <th width="15%">Item Code</th>
                    <th>Description</th>
                    <th width="10%" class="text-center">Qty</th>
                    <th width="15%" class="text-right">Unit Price (Rs.)</th>
                    <th width="15%" class="text-right">Total (Rs.)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $index => $item)
                <tr class="items-row">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->product_code }}</td>
                    <td>
                        <strong>{{ $item->product_name }}</strong>
                        @if($item->product_model)
                            <br><small>Model: {{ $item->product_model }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right amount">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right amount">{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td>Subtotal:</td>
                    <td class="amount">Rs. {{ number_format($sale->subtotal, 2) }}</td>
                </tr>
                @if($sale->discount_amount > 0)
                <tr>
                    <td>Discount:</td>
                    <td class="amount">- Rs. {{ number_format($sale->discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr class="grand-total">
                    <td>Grand Total:</td>
                    <td class="amount">Rs. {{ number_format($sale->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td>Paid Amount:</td>
                    <td class="amount">Rs. {{ number_format($sale->paid_amount ?? 0, 2) }}</td>
                </tr>
                <tr class="balance-due">
                    <td>Balance Due:</td>
                    <td class="amount">Rs. {{ number_format($sale->due_amount ?? ($sale->total_amount - ($sale->paid_amount ?? 0)), 2) }}</td>
                </tr>
            </table>
        </div>
        <div class="notes-section">
            @if($sale->notes)
            <div style="margin-bottom: 20px;">
                <strong>Notes:</strong><br>
                {!! nl2br(e($sale->notes)) !!}
            </div>
            @endif
        </div>
        
        <div class="footer">
            <p><strong>Thank you for your business!</strong></p>
            <p>Goods return will be accepted within 10 days only. Electrical and body parts are non-returnable.</p>
            <p>Invoice generated on: {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>