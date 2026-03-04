<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->number ?? $invoice->id }}</title>
    <style>
        body {
            font-family: 'dejavu sans', 'sans-serif';
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .invoice-box {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            background-color: #fff;
        }

        .header {
            width: 100%;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #0d6efd;
        }

        .header table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-left {
            text-align: left;
            vertical-align: top;
        }

        .header-right {
            text-align: right;
            vertical-align: top;
        }

        .logo {
            max-width: 150px;
            max-height: 80px;
        }

        .company-details {
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
            text-align: center;
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

        .info-row {
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            color: #666;
            display: inline-block;
            min-width: 100px;
        }

        .info-value {
            display: inline-block;
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

        .status-void {
            background-color: #dc3545;
            color: white;
        }

        .status-uncollectible {
            background-color: #343a40;
            color: white;
        }

        .status-completed {
            background-color: #28a745;
            color: white;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border: 1px solid #e5e7eb;
        }

        .table th {
            background-color: #f9fafb;
            color: #374151;
            padding: 12px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            border-bottom: 2px solid #e5e7eb;
        }

        .table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
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

        /* Tax and Totals Grid Layout - Matching View Modal */
        .totals-grid {
            margin-top: 30px;
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .totals-row {
            display: table-row;
        }

        .totals-left {
            display: table-cell;
            width: 50%;
            padding-right: 15px;
            vertical-align: top;
        }

        .totals-right {
            display: table-cell;
            width: 50%;
            padding-left: 15px;
            vertical-align: top;
        }

        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 12px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }

        .tax-table, .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .tax-table td, .summary-table td {
            padding: 5px 0;
        }

        .tax-table .tax-name {
            color: #6b7280;
        }

        .tax-table .tax-rate {
            text-align: right;
            color: #6b7280;
            padding-right: 10px;
        }

        .tax-table .tax-amount {
            text-align: right;
            color: #111827;
            font-weight: 500;
        }

        .summary-table .summary-label {
            color: #6b7280;
            text-align: left;
        }

        .summary-table .summary-value {
            text-align: right;
            color: #111827;
            font-weight: 500;
        }

        .summary-table .total-row {
            font-weight: 700;
            border-top: 2px solid #e5e7eb;
        }

        .summary-table .total-row td {
            padding-top: 8px;
            color: #2563eb;
            font-size: 14px;
        }

        .summary-table .paid-row td {
            color: #10b981;
        }

        .summary-table .due-row td {
            color: #ef4444;
            font-weight: 700;
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
                    @if (!empty($company['logo']) && file_exists($company['logo']))
                        <img src="{{ $company['logo'] }}" alt="{{ $company['name'] }}" class="logo">
                    @else
                        <h2 style="color: #0d6efd; margin:0;">{{ $company['name'] }}</h2>
                    @endif
                </td>

                <td class="header-right">
                    <div class="company-name">{{ $company['name'] }}</div>
                    <div class="company-details">
                        <div>{{ $company['address'] }}</div>
                        <div>{{ $company['city'] }}, {{ $company['state'] }} {{ $company['zip'] }}</div>
                        <div>{{ $company['country'] }}</div>
                        <div>Phone: {{ $company['phone'] }}</div>
                        <div>Email: {{ $company['email'] }}</div>
                        @if (!empty($company['tax_id']))
                            <div>Tax ID: {{ $company['tax_id'] }}</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <!-- Invoice Title -->
        <div class="invoice-title">INVOICE</div>

        <table class="invoice-info">
            <tr>
                <td class="invoice-info-left">
                    <div class="info-label">Bill To:</div>
                    <div class="info-value" style="margin-top: 5px;">
                        <strong>{{ $user->name ?? $invoice->user->name ?? 'N/A' }}</strong><br>
                        {{ $user->email ?? $invoice->user->email ?? 'N/A' }}<br>

                        @if(!empty($billingAddress))
                            @if(!empty($billingAddress['line1']))
                                {{ $billingAddress['line1'] }}<br>
                            @endif
                            @if(!empty($billingAddress['line2']))
                                {{ $billingAddress['line2'] }}<br>
                            @endif
                            @if(!empty($billingAddress['city']) || !empty($billingAddress['state']))
                                {{ $billingAddress['city'] ?? '' }} {{ $billingAddress['state'] ?? '' }} {{ $billingAddress['postal_code'] ?? '' }}<br>
                            @endif
                            @if(!empty($billingAddress['country']))
                                {{ $billingAddress['country'] }}<br>
                            @endif
                        @elseif($invoice->user->billing_address)
                            @php $addr = $invoice->user->billing_address; @endphp
                            @if(is_array($addr))
                                {{ $addr['line1'] ?? '' }}<br>
                                {{ $addr['line2'] ?? '' }}<br>
                                {{ $addr['city'] ?? '' }} {{ $addr['state'] ?? '' }} {{ $addr['postal_code'] ?? '' }}<br>
                                {{ $addr['country'] ?? '' }}
                            @endif
                        @endif
                    </div>
                </td>

                <td class="invoice-info-right">
                    <div class="info-row">
                        <span class="info-label">Invoice Number:</span>
                        <span class="info-value"><strong>{{ $invoice->number ?? $invoice->id }}</strong></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Invoice Date:</span>
                        <span class="info-value">{{ $invoice->formatted_issue_date ?? date('F j, Y', strtotime($invoice->issue_date)) }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Due Date:</span>
                        <span class="info-value">{{ $invoice->formatted_due_date ?? ($invoice->due_date ? date('F j, Y', strtotime($invoice->due_date)) : 'N/A') }}</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Status:</span>
                        <span class="info-value">
                            <span class="status-badge status-{{ $invoice->status ?? 'draft' }}">
                                {{ ucfirst($invoice->status ?? 'draft') }}
                            </span>
                        </span>
                    </div>

                    @if(!empty($subscription) && !empty($subscription->plan))
                        <div class="info-row">
                            <span class="info-label">Subscription:</span>
                            <span class="info-value">
                                {{ $subscription->plan->name ?? 'N/A' }}
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
                @forelse($lineItems as $item)
                    <tr>
                        <td>{{ $item['description'] ?? 'Item' }}</td>
                        <td style="text-align: center;">{{ $item['quantity'] ?? 1 }}</td>
                        <td style="text-align: right;">
                            {{ number_format(floatval($item['unit_price'] ?? $item['amount'] ?? 0), 2) }}
                            {{ $invoice->currency ?? 'USD' }}
                        </td>
                        <td style="text-align: right;">
                            {{ number_format(floatval(($item['amount'] ?? 0) * ($item['quantity'] ?? 1)), 2) }}
                            {{ $invoice->currency ?? 'USD' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center;">No items</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Tax and Totals Grid - Matching View Modal Layout -->
        <div class="totals-grid">
            <div class="totals-row">
                <!-- Left Column - Tax Information -->
                <div class="totals-left">
                    <div class="section-title">Tax Information</div>
                    <table class="tax-table">
                        @if(!empty($taxRates) && is_array($taxRates))
                            @if(isset($taxRates['name']))  {{-- Single tax rate as object --}}
                                <tr>
                                    <td class="tax-name">{{ $taxRates['name'] ?? 'Tax' }}</td>
                                    <td class="tax-rate">{{ $taxRates['rate'] ?? 0 }}%</td>
                                    <td class="tax-amount">{{ number_format(floatval($taxRates['amount'] ?? $invoice->tax ?? 0), 2) }} {{ $invoice->currency ?? 'USD' }}</td>
                                </tr>
                            @else  {{-- Multiple tax rates as array --}}
                                @foreach($taxRates as $tax)
                                    <tr>
                                        <td class="tax-name">{{ $tax['name'] ?? 'Tax' }}</td>
                                        <td class="tax-rate">{{ $tax['rate'] ?? 0 }}%</td>
                                        <td class="tax-amount">{{ number_format(floatval($tax['amount'] ?? 0), 2) }} {{ $invoice->currency ?? 'USD' }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        @elseif($invoice->tax > 0)
                            <tr>
                                <td class="tax-name">Tax</td>
                                <td class="tax-rate">-</td>
                                <td class="tax-amount">{{ number_format(floatval($invoice->tax), 2) }} {{ $invoice->currency ?? 'USD' }}</td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="3" style="color: #9ca3af; text-align: center;">No tax information</td>
                            </tr>
                        @endif
                    </table>
                </div>

                <!-- Right Column - Summary Totals -->
                <div class="totals-right">
                    <div class="section-title">Summary</div>
                    <table class="summary-table">
                        <tr>
                            <td class="summary-label">Subtotal:</td>
                            <td class="summary-value">{{ number_format(floatval($invoice->subtotal ?? 0), 2) }} {{ $invoice->currency ?? 'USD' }}</td>
                        </tr>
                        <tr>
                            <td class="summary-label">Tax:</td>
                            <td class="summary-value">{{ number_format(floatval($invoice->tax ?? 0), 2) }} {{ $invoice->currency ?? 'USD' }}</td>
                        </tr>

                        @if(!empty($discounts) && is_array($discounts) && !empty($discounts['amount']))
                            <tr>
                                <td class="summary-label">{{ $discounts['name'] ?? 'Discount' }}:</td>
                                <td class="summary-value" style="color: #10b981;">-{{ number_format(floatval($discounts['amount'] ?? 0), 2) }} {{ $invoice->currency ?? 'USD' }}</td>
                            </tr>
                        @endif

                        <tr class="total-row">
                            <td class="summary-label">Total:</td>
                            <td class="summary-value" style="color: #2563eb;">{{ number_format(floatval($invoice->total ?? 0), 2) }} {{ $invoice->currency ?? 'USD' }}</td>
                        </tr>

                        <!-- Paid and Balance Due - Always displayed -->
                        <tr class="paid-row">
                            <td class="summary-label">Paid:</td>
                            <td class="summary-value">{{ number_format(floatval($invoice->amount_paid ?? 0), 2) }} {{ $invoice->currency ?? 'USD' }}</td>
                        </tr>
                        <tr class="due-row">
                            <td class="summary-label">Balance Due:</td>
                            <td class="summary-value">{{ number_format(floatval($invoice->amount_due ?? ($invoice->total - ($invoice->amount_paid ?? 0))), 2) }} {{ $invoice->currency ?? 'USD' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
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
        @if(!empty($invoice->metadata) && is_array($invoice->metadata) && !empty($invoice->metadata['notes']))
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
