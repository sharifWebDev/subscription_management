<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->number }}</title>
    <style>
        body {
            font-family: 'dejavusans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            /* border: 1px solid #eee; */
            background-color: #fff;
        }

        .header {
            width: 100%;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #0d6efd;
            border-collapse: collapse;
        }

        .header td {
            vertical-align: top;
        }

        .header-left {
            width: 50%;
            text-align: left;
        }

        .header-right {
            width: 50%;
            text-align: right;
        }

        .logo {
            max-width: 150px;
            max-height: 80px;
        }

        .company-details {
            text-align: right;
            color: #666;
            font-size: 11px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #0d6efd;
            margin-bottom: 5px;
        }

        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #0d6efd;
            margin-bottom: 20px;
        }

        .invoice-info {
            width: 100%;
            margin-bottom: 30px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border-collapse: collapse;
        }

        .invoice-info td {
            vertical-align: top;
            padding: 15px;
        }

        .invoice-info-left {
            width: 50%;
            text-align: left;
        }

        .invoice-info-right {
            width: 50%;
            text-align: right;
        }

        .invoice-info-right {
            text-align: right;
        }

        .info-label {
            font-weight: bold;
            color: #666;
            margin-bottom: 3px;
        }

        .info-value {
            margin-bottom: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-paid {
            background-color: #28a745;
            color: white;
        }

        .status-open {
            background-color: #ffc107;
            color: #333;
        }

        .status-draft {
            background-color: #6c757d;
            color: white;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .table th {
            background-color: #0d6efd;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }

        .table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .table .item-description {
            width: 50%;
        }

        .table .item-qty {
            width: 10%;
            text-align: center;
        }

        .table .item-price {
            width: 20%;
            text-align: right;
        }

        .table .item-total {
            width: 20%;
            text-align: right;
        }

        .totals {
            margin-top: 30px;
            text-align: right;
        }

        .totals table {
            width: 100%;
            max-width: 400px;
            margin-left: auto;
        }

        .totals td {
            padding: 5px 10px;
        }

        .totals .total-label {
            font-weight: bold;
        }

        .totals .total-amount {
            text-align: right;
        }

        .grand-total {
            font-size: 16px;
            font-weight: bold;
            color: #0d6efd;
            border-top: 2px solid #0d6efd;
        }

        .payment-info {
            margin-top: 40px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            font-size: 11px;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .notes {
            margin-top: 30px;
            padding: 15px;
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 3px;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <!-- Header -->
        <table class="header">
            <tr>
                <td class="header-left">
                    @if (file_exists($company['logo']))
                        <img src="{{ $company['logo'] }}" alt="{{ $company['name'] }}" class="logo">
                    @else
                        <h2>{{ $company['name'] }}</h2>
                    @endif
                </td>

                <td class="header-right">
                    <div class="company-name">{{ $company['name'] }}</div>
                    <div>{{ $company['address'] }}</div>
                    <div>{{ $company['city'] }}, {{ $company['state'] }} {{ $company['zip'] }}</div>
                    <div>{{ $company['country'] }}</div>
                    <div>Phone: {{ $company['phone'] }}</div>
                    <div>Email: {{ $company['email'] }}</div>
                    @if ($company['tax_id'])
                        <div>Tax ID: {{ $company['tax_id'] }}</div>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Invoice Title -->
        <div class="invoice-title">INVOICE</div>

        <table class="invoice-info">
            <tr>
                <td class="invoice-info-left">
                    <div class="info-label">Bill To:</div>
                    <div class="info-value">
                        <strong>{{ $invoice->user->name }}</strong><br>
                        {{ $invoice->user->email }}<br>
                        @if ($invoice->user->phone)
                            {{ $invoice->user->phone }}<br>
                        @endif
                        @if ($invoice->user->billing_address)
                            {{ $invoice->user->billing_address->line1 ?? '' }}<br>
                            {{ $invoice->user->billing_address->line2 ?? '' }}<br>
                            {{ $invoice->user->billing_address->city ?? '' }},
                            {{ $invoice->user->billing_address->state ?? '' }}
                            {{ $invoice->user->billing_address->postal_code ?? '' }}<br>
                            {{ $invoice->user->billing_address->country ?? '' }}
                        @endif
                    </div>
                </td>

                <td class="invoice-info-right">

                    <div class="info-row">
                        <span class="info-label">Invoice Number:</span>
                        <span class="info-value"><strong>{{ $invoice->number }}</strong></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Invoice Date:</span>
                        <span class="info-value">{{ $invoice->issue_date->format('F j, Y') }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Due Date:</span>
                        <span class="info-value">{{ $invoice->due_date->format('F j, Y') }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Status:</span>
                        <span class="info-value">
                            <span class="status-badge status-{{ $invoice->status }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </span>
                    </div>

                    @if ($invoice->subscription)
                        <div class="info-row">
                            <span class="info-label">Subscription:</span>
                            <span class="info-value">
                                {{ $invoice->subscription->plan->name ?? 'N/A' }}
                            </span>
                        </div>
                    @endif

                </td>
            </tr>
        </table>

        <!-- Line Items Table -->
        <table class="table">
            <thead>
                <tr>
                    <th class="item-description">Description</th>
                    <th class="item-qty">Quantity</th>
                    <th class="item-price">Unit Price</th>
                    <th class="item-total">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->line_items as $item)
                    <tr>
                        <td>{{ $item['description'] ?? 'Item' }}</td>
                        <td style="text-align: center;">{{ $item['quantity'] ?? 1 }}</td>
                        <td style="text-align: right;">
                            {{ number_format($item['unit_price'] ?? ($item['amount'] ?? 0), 2) }}
                            {{ $invoice->currency }}
                        </td>
                        <td style="text-align: right;">
                            {{ number_format(($item['amount'] ?? 0) * ($item['quantity'] ?? 1), 2) }}
                            {{ $invoice->currency }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center;">No items</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <table>
                <tr>
                    <td class="total-label">Subtotal:</td>
                    <td class="total-amount">{{ number_format($invoice->subtotal, 2) }} {{ $invoice->currency }}</td>
                </tr>

                @if (!empty($invoice->tax_rates))
                    @foreach ($invoice->tax_rates as $tax)
                        <tr>
                            <td class="total-label">{{ $tax['name'] ?? 'Tax' }} ({{ $tax['rate'] ?? 0 }}%):</td>
                            <td class="total-amount">{{ number_format($tax['amount'] ?? 0, 2) }}
                                {{ $invoice->currency }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="total-label">Tax:</td>
                        <td class="total-amount">{{ number_format($invoice->tax, 2) }} {{ $invoice->currency }}</td>
                    </tr>
                @endif

                @if (!empty($invoice->discounts))
                    @foreach ($invoice->discounts as $discount)
                        <tr>
                            <td class="total-label">{{ $discount['name'] ?? 'Discount' }}:</td>
                            <td class="total-amount">-{{ number_format($discount['amount'] ?? 0, 2) }}
                                {{ $invoice->currency }}</td>
                        </tr>
                    @endforeach
                @endif

                <tr class="grand-total">
                    <td class="total-label">Total:</td>
                    <td class="total-amount">{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</td>
                </tr>

                @if ($invoice->amount_paid > 0)
                    <tr>
                        <td class="total-label">Paid:</td>
                        <td class="total-amount">{{ number_format($invoice->amount_paid, 2) }}
                            {{ $invoice->currency }}</td>
                    </tr>
                    <tr>
                        <td class="total-label">Balance Due:</td>
                        <td class="total-amount">{{ number_format($invoice->amount_due, 2) }} {{ $invoice->currency }}
                        </td>
                    </tr>
                @endif
            </table>
        </div>

        <!-- Payment Information -->
        <div class="payment-info">
            <strong>Payment Information:</strong><br>
            Please make payment to:<br>
            <strong>Bank:</strong> {{ config('payment.bank_name', 'Example Bank') }}<br>
            <strong>Account Name:</strong> {{ config('payment.account_name', config('app.name')) }}<br>
            <strong>Account Number:</strong> {{ config('payment.account_number', '1234567890') }}<br>
            <strong>Routing Number:</strong> {{ config('payment.routing_number', '021000021') }}<br>
            <strong>SWIFT Code:</strong> {{ config('payment.swift_code', 'EXBKUS33') }}
        </div>

        <!-- Notes -->
        @if (isset($invoice->metadata['notes']))
            <div class="notes">
                <strong>Notes:</strong><br>
                {{ $invoice->metadata['notes'] }}
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your business!</p>
            <p>{{ $company['name'] }} | {{ $company['email'] }} | {{ $company['phone'] }}</p>
            <p>This is a computer-generated invoice. No signature is required.</p>
        </div>
    </div>
</body>

</html>
