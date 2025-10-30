<!DOCTYPE html>
<html>
<head>
    <title>Payment Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .company-name { font-size: 24px; font-weight: bold; color: #333; }
        .receipt-title { font-size: 20px; margin: 10px 0; }
        .details-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .details-table td { padding: 8px; border: 1px solid #ddd; }
        .details-table .label { font-weight: bold; background-color: #f8f9fa; width: 30%; }
        .total-row { background-color: #f8f9fa; font-weight: bold; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #666; }
        .signature-area { margin-top: 60px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">USN Auto Parts</div>
        <div class="receipt-title">PAYMENT RECEIPT</div>
    </div>

    <table class="details-table">
        <tr>
            <td class="label">Receipt ID</td>
            <td>#{{ $payment->id }}</td>
            <td class="label">Payment Date</td>
            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
        </tr>
        <tr>
            <td class="label">Customer Name</td>
            <td>{{ $customer->name }}</td>
            <td class="label">Phone</td>
            <td>{{ $customer->phone }}</td>
        </tr>
        <tr>
            <td class="label">Payment Method</td>
            <td class="text-capitalize">{{ $payment->payment_method }}</td>
            <td class="label">Reference No</td>
            <td>{{ $payment->reference_number ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Invoice Number</td>
            <td>{{ $sale->invoice_number }}</td>
            <td class="label">Sale ID</td>
            <td>{{ $sale->sale_id }}</td>
        </tr>
    </table>

    <table class="details-table">
        <tr class="total-row">
            <td class="label">Amount Paid</td>
            <td class="text-right">Rs.{{ number_format($payment->amount, 2) }}</td>
        </tr>
    </table>

    @if($payment->notes)
    <div style="margin-top: 20px;">
        <strong>Notes:</strong>
        <p>{{ $payment->notes }}</p>
    </div>
    @endif

    <div class="signature-area">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;">
                    <div style="border-top: 1px solid #333; padding-top: 10px;">
                        Customer Signature
                    </div>
                </td>
                <td style="width: 50%;">
                    <div style="border-top: 1px solid #333; padding-top: 10px;">
                        Received By: {{ $received_by }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Thank you for your payment!</p>
        <p>Generated on: {{ now()->format('M d, Y h:i A') }}</p>
    </div>
</body>
</html>