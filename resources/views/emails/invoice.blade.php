<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #1a1a2e; color: #fff; padding: 30px; }
        .body { padding: 30px; }
        .invoice-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .invoice-table th, .invoice-table td { padding: 10px; border: 1px solid #dee2e6; text-align: left; }
        .invoice-table th { background: #f8f9fa; }
        .total-row { font-weight: bold; background: #f8f9fa; }
        .badge-paid { background: #28a745; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 12px; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #6c757d; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2 style="margin:0;">{{ get_setting('site_name', 'Alborada Box') }}</h2>
        <p style="margin:5px 0 0;opacity:.7;">Invoice</p>
    </div>
    <div class="body">
        <p>Hello <strong>{{ $invoice->user->name }}</strong>,</p>
        <p>Please find your invoice attached. Here is a summary:</p>

        <table class="invoice-table">
            <tr><th>Invoice #</th><td>{{ $invoice->invoice_number }} <span class="badge-paid">{{ strtoupper($invoice->status) }}</span></td></tr>
            <tr><th>Date</th><td>{{ $invoice->created_at->format('M d, Y') }}</td></tr>
            @if($invoice->subscription)
            <tr><th>Plan</th><td>{{ $invoice->subscription->plan->title ?? 'N/A' }}</td></tr>
            @endif
            <tr><th>Amount</th><td>${{ number_format($invoice->amount, 2) }}</td></tr>
            @if($invoice->tax_amount > 0)
            <tr><th>Tax</th><td>${{ number_format($invoice->tax_amount, 2) }}</td></tr>
            @endif
            <tr class="total-row"><th>Total</th><td>${{ number_format($invoice->total_amount, 2) }}</td></tr>
        </table>

        @if($invoice->notes)
        <p><strong>Notes:</strong> {{ $invoice->notes }}</p>
        @endif

        <hr style="border:none;border-top:1px solid #eee;margin:20px 0;">
        <p>Need help? <a href="{{ route('member.tickets.create') }}">Contact support</a>.</p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ get_setting('site_name', 'Alborada Box') }}. All rights reserved.</p>
    </div>
</div>
</body>
</html>
