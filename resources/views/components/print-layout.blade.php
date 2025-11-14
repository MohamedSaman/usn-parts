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
            margin: 0;
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
            padding: 10mm;
            margin: 0 auto;
            background: white;
            position: relative;
            box-sizing: border-box;
        }

        /* Global Header Styles */
        .global-header {
            border-bottom: 3px solid #3b5b0c;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        @media print {
            .global-header {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                width: 100%;
                z-index: 1000;
                background: white;
                margin-bottom: 0;
                padding-top: 0;
            }

            .global-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                width: 100%;
                z-index: 1000;
                background: white;
                margin-top: 0;
                padding-bottom: 0;
            }

            .print-content {
                margin-top: 140px;
                /* Height of header */
                margin-bottom: 180px;
                /* Height of footer */
            }

            body,
            html {
                margin: 0;
                padding: 0;
            }
        }

        .global-header .d-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .global-header img {
            max-height: 50px;
        }

        .global-header h2 {
            font-size: 2.5rem;
            letter-spacing: 2px;
            font-weight: bold;
            margin-bottom: 0;
        }

        .global-header .text-muted {
            color: #666;
            font-size: 0.875rem;
        }

        .global-header h5,
        .global-header h6 {
            margin-bottom: 0;
            font-weight: bold;
        }

        .global-header h6 {
            color: #666;
            margin-top: 10px;
        }

        .global-header hr {
            border-top: 2px solid #000;
            margin: 0.25rem 0;
        }

        /* Content Area */
        .print-content {
            min-height: calc(297mm - 200px);
            margin-bottom: 20px;
        }

        /* Global Footer Styles */
        .global-footer {
            position: absolute;
            bottom: 15mm;
            left: 10mm;
            right: 10mm;
            margin-top: 20px;
        }

        .global-footer .row {
            display: flex;
            margin-left: 0;
            margin-right: 0;
        }

        .global-footer .col-4 {
            flex: 0 0 30%;
            max-width: 30%;
            text-align: center;
            padding: 0 10px;
        }

        .global-footer img {
            height: 35px;
            margin: auto;
        }

        .global-footer .border-top {
            border-top: 1px solid #3b5b0c;
            padding-top: 10px;
        }

        .global-footer .text-center {
            text-align: center;
            font-size: 10px;
            color: #666;
            margin: 2px 0;
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
        }

        .invoice-info-row p {
            margin: 2px 0;
            line-height: 1.4;
        }

        .invoice-info-row table {
            font-size: 11px;
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
            border-top: 1px solid #000;
            font-size: 11px;
            padding: 7px 8px;
            background-color: #f8f9fa;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
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

        .pt-3 {
            padding-top: 1rem;
        }

        /* Hide on screen, show on print */
        @media screen {
            body {
                background: #f5f5f5;
                padding: 20px 0;
            }

            .print-container {
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
        }
    </style>
</head>

<body>
    <div class="print-container">
        <!-- Global Header -->
        <div class="global-header">
            <div class="d-flex align-items-center justify-content-between mb-3">
                {{-- Left: Logo --}}
                <div style="flex: 0 0 150px;">
                    <img src="{{ asset('images/USN.png') }}" alt="Logo" class="img-fluid" style="max-height:50px;">
                </div>
                {{-- Center: Company Name --}}
                <div class="text-center" style="flex: 1;">
                    <h2 class="mb-0 fw-bold" style="font-size: 2.5rem; letter-spacing: 2px;">USN AUTO PARTS</h2>
                    <p class="mb-0 text-muted small">IMPORTERS & DISTRIBUTERS OF MAHINDRA AND TATA PARTS</p>
                </div>
                {{-- Right: Motor Parts & Invoice --}}
                <div class="text-end" style="flex: 0 0 150px;">
                    <h3 class="mb-0 fw-bold">MOTOR PARTS</h3>
                    <h6 class="mb-0 text-muted">{{ $documentType ?? 'INVOICE' }}</h6>
                </div>
            </div>
        </div>

        <!-- Dynamic Content Area -->
        <div class="print-content">
            {{ $slot }}
        </div>

        <!-- Global Footer -->
        <div class="global-footer">
            <div class="row text-center mb-3">
                <div class="col-4">
                    <p class=""><strong>.............................</strong></p>
                    <p class="mb-2"><strong>Checked By</strong></p>
                    <img src="{{ asset('images/tata.png') }}" alt="TATA" style="height: 35px;margin: auto;">
                </div>
                <div class="col-4">
                    <p class=""><strong>.............................</strong></p>
                    <p class="mb-2"><strong>Authorized Officer</strong></p>
                    <img src="{{ asset('images/USN.png') }}" alt="USN" style="height: 35px;margin: auto;">
                </div>
                <div class="col-4">
                    <p class=""><strong>.............................</strong></p>
                    <p class="mb-2"><strong>Customer Stamp</strong></p>
                    <img src="{{ asset('images/mahindra.png') }}" alt="Mahindra" style="height: 35px;margin: auto;">
                </div>
            </div>
            <div class="border-top pt-3">
                <p class="text-center"><strong>ADDRESS :</strong> 103 H, Yatiyanthota Road, Seethawaka, Avissawella</p>
                <p class="text-center"><strong>TEL :</strong> (076) 9085252, <strong>EMAIL :</strong> autopartsusn@gmail.com</p>
                <p class="text-center mt-2" style="font-size: 11px;"><strong>Goods return will be accepted within 10 days only. Electrical and body parts non-returnable.</strong></p>
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