<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 13px; color: #212529; margin: 0; padding: 30px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .company-name { font-size: 22px; font-weight: bold; color: #1a1a2e; }
        .company-sub { color: #6c757d; font-size: 12px; }
        .invoice-title { text-align: right; }
        .invoice-title h1 { font-size: 28px; color: #1a1a2e; margin: 0; }
        .invoice-title .badge { display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; color: #fff; }
        .badge-paid { background: #28a745; }
        .badge-draft { background: #6c757d; }
        .badge-sent { background: #007bff; }
        .badge-void { background: #dc3545; }
        .meta-row { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .meta-box { width: 48%; }
        .meta-box h4 { margin: 0 0 8px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #6c757d; }
        .meta-box p { margin: 2px 0; }
        hr { border: none; border-top: 2px solid #1a1a2e; margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #1a1a2e; color: #fff; padding: 10px 12px; text-align: left; font-size: 12px; }
        td { padding: 10px 12px; border-bottom: 1px solid #dee2e6; }
        .subtotal-row td { border-bottom: none; }
        .total-row td { font-weight: bold; font-size: 15px; background: #f8f9fa; border-top: 2px solid #1a1a2e; }
        .footer { margin-top: 40px; text-align: center; font-size: 11px; color: #6c757d; }
    </style>
</head>
<body>

<div class="header">
    <div>
        <div class="company-name">{{ get_setting('site_name', 'Moissanite Radiance') }}</div>
        <div class="company-sub">{{ get_setting('site_email', '') }}</div>
        <div class="company-sub">{{ get_setting('site_address', '') }}</div>
    </div>
    <div class="invoice-title">
        <h1>INVOICE</h1>
        <div>{{ $invoice->invoice_number }}</div>
        <div><span class="badge badge-{{ $invoice->status }}">{{ strtoupper($invoice->status) }}</span></div>
    </div>
</div>

<div class="meta-row">
    <div class="meta-box">
        <h4>Billed To</h4>
        <p><strong>{{ $invoice->user->name }}</strong></p>
        <p>{{ $invoice->user->email }}</p>
        @if($invoice->user->phone)
        <p>{{ $invoice->user->phone }}</p>
        @endif
    </div>
    <div class="meta-box" style="text-align:right;">
        <h4>Invoice Details</h4>
        <p><strong>Date:</strong> {{ $invoice->created_at->format('M d, Y') }}</p>
        <p><strong>Due:</strong> {{ $invoice->due_date?->format('M d, Y') ?? 'N/A' }}</p>
        @if($invoice->paid_at)
        <p><strong>Paid:</strong> {{ $invoice->paid_at->format('M d, Y') }}</p>
        @endif
    </div>
</div>

<hr>

<table>
    <thead>
        <tr>
            <th style="width:50%">Description</th>
            <th style="width:20%;text-align:center;">Period</th>
            <th style="width:15%;text-align:right;">Price</th>
            <th style="width:15%;text-align:right;">Amount</th>
        </tr>
    </thead>
    <tbody>
        @if($invoice->subscription && $invoice->subscription->plan)
        <tr>
            <td>
                <strong>{{ $invoice->subscription->plan->title }}</strong><br>
                <small style="color:#6c757d;">
                    {{ $invoice->subscription->plan->max_connections ?? 1 }} connection(s) &bull;
                    {{ $invoice->subscription->plan->streaming_quality ?? 'HD' }} &bull;
                    {{ $invoice->subscription->plan->duration_days }} days
                </small>
            </td>
            <td style="text-align:center;">
                {{ $invoice->subscription->start_date?->format('M d, Y') ?? '—' }}<br>
                <small>to</small><br>
                {{ $invoice->subscription->expires_at?->format('M d, Y') ?? '—' }}
            </td>
            <td style="text-align:right;">${{ number_format($invoice->amount, 2) }}</td>
            <td style="text-align:right;">${{ number_format($invoice->amount, 2) }}</td>
        </tr>
        @else
        <tr>
            <td colspan="2">IPTV Subscription</td>
            <td style="text-align:right;">${{ number_format($invoice->amount, 2) }}</td>
            <td style="text-align:right;">${{ number_format($invoice->amount, 2) }}</td>
        </tr>
        @endif
    </tbody>
    <tfoot>
        <tr class="subtotal-row">
            <td colspan="3" style="text-align:right;">Subtotal</td>
            <td style="text-align:right;">${{ number_format($invoice->amount, 2) }}</td>
        </tr>
        @if($invoice->tax_amount > 0)
        <tr class="subtotal-row">
            <td colspan="3" style="text-align:right;">Tax</td>
            <td style="text-align:right;">${{ number_format($invoice->tax_amount, 2) }}</td>
        </tr>
        @endif
        <tr class="total-row">
            <td colspan="3" style="text-align:right;">TOTAL</td>
            <td style="text-align:right;">${{ number_format($invoice->total_amount, 2) }}</td>
        </tr>
    </tfoot>
</table>

@if($invoice->notes)
<div style="margin-top:20px;padding:12px;background:#f8f9fa;border-radius:4px;">
    <strong>Notes:</strong> {{ $invoice->notes }}
</div>
@endif

<div class="footer">
    <p>Thank you for your business — {{ get_setting('site_name', 'Moissanite Radiance') }}</p>
    <p>{{ get_setting('site_email', '') }} &bull; {{ get_setting('site_url', '') }}</p>
</div>

</body>
</html>
