<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Print Document' }}</title>
    <style>
        /* Global Print Layout Styles */
        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            background: white;
            color: #000;
        }

        .print-container {
            width: 210mm;
            min-height: 297mm;
            padding: 0;
            margin: 0 auto;
            background: white;
            position: relative;
        }

        /* Global Header Styles */
        .global-header {
            border-bottom: 3px solid #3b5b0c;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .global-header .d-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .global-header .header-left {
            flex: 0 0 100px;
            max-width: 100px;
        }

        .global-header .header-left img {
            max-height: 60px;
            max-width: 100%;
            height: auto;
            width: auto;
            display: block;
        }

        .global-header .header-center {
            flex: 1;
            text-align: center;
            padding: 0 10px;
            min-width: 0;
        }

        .global-header .header-center h2 {
            font-size: 1.5rem;
            letter-spacing: 1.5px;
            font-weight: bold;
            margin: 0 0 3px 0;
            line-height: 1.2;
            white-space: nowrap;
        }

        .global-header .header-center p {
            color: #666;
            font-size: 0.65rem;
            margin: 0;
            line-height: 1.3;
        }

        .global-header .header-right {
            flex: 0 0 100px;
            max-width: 100px;
            text-align: right;
        }

        .global-header .header-right h3 {
            margin: 0 0 3px 0;
            font-weight: bold;
            font-size: 0.85rem;
            line-height: 1.2;
        }

        .global-header .header-right h6 {
            margin: 0;
            color: #666;
            font-size: 0.75rem;
            line-height: 1.2;
        }

        .global-header hr {
            border: 0;
            border-top: 2px solid #000;
            margin: 8px 0 0 0;
        }

        /* Content Area */
        .print-content {
            min-height: 500px;
            margin-bottom: 20px;
        }

        /* Global Footer Styles */
        .global-footer {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .global-footer .row {
            display: flex;
            justify-content: space-between;
            margin: 0 0 15px 0;
            text-align: center;
        }

        .global-footer .col-4 {
            flex: 0 0 32%;
            max-width: 32%;
            padding: 0 5px;
        }

        .global-footer .col-4 p {
            margin: 0 0 6px 0;
            font-size: 11px;
        }

        .global-footer .col-4 img {
            height: 30px;
            margin: 0 auto;
            display: block;
        }

        .global-footer .border-top {
            border-top: 1px solid #3b5b0c;
            padding-top: 10px;
            margin-top: 0;
        }

        .global-footer .footer-info p {
            text-align: center;
            font-size: 10px;
            color: #333;
            margin: 4px 0;
            line-height: 1.4;
        }

        .global-footer .footer-info .terms {
            font-weight: bold;
            margin-top: 8px;
        }

        /* Sale Receipt Specific Styles */
        .invoice-info-row {
            display: flex;
            margin-bottom: 15px;
            font-size: 11px;
        }

        .invoice-info-row .col-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 10px;
        }

        .invoice-info-row .col-6:first-child {
            padding-left: 0;
        }

        .invoice-info-row .col-6:last-child {
            padding-right: 0;
        }

        .invoice-info-row p {
            margin: 2px 0;
            line-height: 1.5;
        }

        .invoice-info-row table {
            font-size: 11px;
            width: 100%;
        }

        .invoice-info-row td {
            padding: 2px 0;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10px;
        }

        .invoice-table th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 6px 8px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }

        .invoice-table td {
            border: 1px solid #000;
            padding: 5px 8px;
        }

        .invoice-table tbody tr {
            page-break-inside: avoid;
        }

        .invoice-table tfoot .totals-row td {
            border-top: 1px solid #000;
            padding: 5px 8px;
            font-weight: bold;
        }

        .invoice-table tfoot .grand-total td {
            border-top: 2px solid #000;
            font-size: 11px;
            padding: 7px 8px;
            background-color: #f8f9fa;
            font-weight: bold;
        }

        /* Utility Classes */
        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-muted {
            color: #666;
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

        .pt-3 {
            padding-top: 1rem;
        }

        .d-flex {
            display: flex;
        }

        .align-items-center {
            align-items: center;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        /* Screen View */
        @media screen {
            body {
                background: #f5f5f5;
                padding: 20px 0;
            }

            .print-container {
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                padding: 15mm;
            }
        }

        /* Print Styles */
        @media print {
            body,
            html {
                margin: 0;
                padding: 0;
                width: 210mm;
                height: 297mm;
            }

            .print-container {
                width: 210mm;
                height: 297mm;
                padding: 0;
                margin: 0;
                box-shadow: none;
            }

            .global-header {
                margin-bottom: 15px;
            }

            .global-footer {
                page-break-inside: avoid;
            }

            /* Prevent content from being cut off */
            .print-content {
                page-break-inside: auto;
            }

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
        }
    </style>
</head>

<body>
    <div class="print-container">
        <!-- Global Header -->
        <div class="global-header">
            <div class="d-flex align-items-center justify-content-between">
                {{-- Left: Logo --}}
                <div class="header-left">
                    <img src="{{ asset('images/USN.png') }}" alt="Logo">
                </div>
                {{-- Center: Company Name --}}
                <div class="header-center">
                    <h2 class="mb-0 fw-bold">USN AUTO PARTS</h2>
                    <p class="mb-0 text-muted">IMPORTERS & DISTRIBUTERS OF MAHINDRA AND TATA PARTS</p>
                </div>
                {{-- Right: Motor Parts & Invoice --}}
                <div class="header-right">
                    <h3 class="mb-0 fw-bold">MOTOR PARTS</h3>
                    <h6 class="mb-0 text-muted">{{ $documentType ?? 'INVOICE' }}</h6>
                </div>
            </div>
            <hr>
        </div>

        <!-- Dynamic Content Area -->
        <div class="print-content">
            {{ $slot }}
        </div>

        <!-- Global Footer -->
        <div class="global-footer">
            <div class="row text-center">
                <div class="col-4">
                    <p><strong>.............................</strong></p>
                    <p><strong>Checked By</strong></p>
                    <img src="{{ asset('images/tata.png') }}" alt="TATA">
                </div>
                <div class="col-4">
                    <p><strong>.............................</strong></p>
                    <p><strong>Authorized Officer</strong></p>
                    <img src="{{ asset('images/USN.png') }}" alt="USN">
                </div>
                <div class="col-4">
                    <p><strong>.............................</strong></p>
                    <p><strong>Customer Stamp</strong></p>
                    <img src="{{ asset('images/mahindra.png') }}" alt="Mahindra">
                </div>
            </div>
            <div class="border-top">
                <div class="footer-info">
                    <p><strong>ADDRESS :</strong> 103 H, Yatiyanthota Road, Seethawaka, Avissawella</p>
                    <p><strong>TEL :</strong> (076) 9085252, <strong>EMAIL :</strong> autopartsusn@gmail.com</p>
                    <p class="terms">Goods return will be accepted within 10 days only. Electrical and body parts non-returnable.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-print script -->
    <script>
        // Auto-trigger print when page loads
        window.onload = function() {
            // Small delay to ensure content is fully rendered
            setTimeout(function() {
                window.print();
            }, 500);
        };

        // Optional: Close window after printing or canceling
        window.onafterprint = function() {
            // You can uncomment the line below to auto-close the window after printing
            // window.close();
        };
    </script>
</body>

</html>