<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $order->invoice_number ?? $order->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #1a1a1a;
            padding: 15px 20px;
        }
        
        .container {
            max-width: 100%;
        }
        
        /* Header Section */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 12px;
        }
        
        .header-left, .header-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .header-right {
            text-align: right;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 4px;
        }
        
        .company-details {
            font-size: 9px;
            color: #666;
            line-height: 1.4;
        }
        
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 4px;
        }
        
        .invoice-meta {
            font-size: 9px;
            color: #666;
            line-height: 1.5;
        }
        
        .invoice-meta strong {
            color: #1a1a1a;
            font-weight: 600;
        }
        
        /* Info Section - Client et Facturation sur même ligne */
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .client-box, .invoice-box {
            display: table-cell;
            width: 48%;
            vertical-align: top;
            padding: 10px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }
        
        .client-box {
            margin-right: 4%;
        }
        
        .box-title {
            font-size: 10px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .box-content {
            font-size: 9px;
            line-height: 1.5;
        }
        
        .box-content p {
            margin: 2px 0;
        }
        
        .box-content strong {
            color: #1a1a1a;
            font-weight: 600;
            display: inline-block;
            min-width: 55px;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .status-paid {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-canceled {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        /* Table Items */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 9px;
        }
        
        .items-table thead {
            background: #f3f4f6;
        }
        
        .items-table th {
            padding: 6px 8px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #d1d5db;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .items-table th.text-center {
            text-align: center;
        }
        
        .items-table th.text-right {
            text-align: right;
        }
        
        .items-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        
        .items-table tbody tr:hover {
            background-color: #f9fafb;
        }
        
        .items-table tbody tr:last-child td {
            border-bottom: 2px solid #d1d5db;
        }
        
        .item-title {
            font-weight: 500;
            color: #1a1a1a;
            margin-bottom: 1px;
        }
        
        .item-ref {
            font-size: 8px;
            color: #9ca3af;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        /* Totals Section */
        .totals-section {
            display: table;
            width: 100%;
            margin-top: 10px;
        }
        
        .totals-left, .totals-right {
            display: table-cell;
            vertical-align: top;
        }
        
        .totals-left {
            width: 55%;
        }
        
        .totals-right {
            width: 45%;
        }
        
        .notes-box {
            padding: 10px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            font-size: 8px;
            color: #666;
            line-height: 1.4;
        }
        
        .notes-title {
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 4px;
            font-size: 9px;
        }
        
        .totals-table {
            width: 100%;
            font-size: 9px;
        }
        
        .totals-table tr {
            line-height: 1.8;
        }
        
        .totals-table td {
            padding: 3px 8px;
        }
        
        .totals-table .label {
            text-align: right;
            color: #666;
            font-weight: 500;
        }
        
        .totals-table .value {
            text-align: right;
            font-weight: 600;
            color: #1a1a1a;
        }
        
        .total-line {
            border-top: 2px solid #2563eb;
            padding-top: 6px !important;
            margin-top: 4px;
        }
        
        .total-line .label {
            font-size: 11px;
            color: #1a1a1a;
            font-weight: bold;
        }
        
        .total-line .value {
            font-size: 12px;
            color: #2563eb;
            font-weight: bold;
        }
        
        /* Footer */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            line-height: 1.4;
        }
        
        .footer-highlight {
            color: #2563eb;
            font-weight: 600;
        }
        
        /* Print Optimization */
        @media print {
            body {
                padding: 10px 15px;
            }
            
            .items-table tbody tr:hover {
                background-color: transparent;
            }
        }
        
        @page {
            margin: 1cm;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <div class="company-name">{{ $company['name'] ?? config('app.name') }}</div>
                <div class="company-details">
                    {{ $company['address'] ?? '123 Rue Example' }}<br>
                    {{ $company['city'] ?? 'Dakar, Sénégal' }}<br>
                    Tél: {{ $company['phone'] ?? '+221 XX XXX XX XX' }} | Email: {{ $company['email'] ?? 'contact@example.com' }}
                </div>
            </div>
            <div class="header-right">
                <div class="invoice-title">FACTURE</div>
                <div class="invoice-meta">
                    <strong>N° Facture:</strong> {{ $order->invoice_number ?? 'INV-' . str_pad($order->id, 6, '0', STR_PAD_LEFT) }}<br>
                    <strong>Date facture:</strong> {{ isset($order->invoice_date) ? $order->invoice_date->format('d/m/Y') : now()->format('d/m/Y') }}<br>
                    <strong>Commande:</strong> #{{ $order->id }}<br>
                    <strong>Date commande:</strong> {{ $order->created_at->format('d/m/Y') }}
                </div>
            </div>
        </div>
        
        <!-- Info Section -->
        <div class="info-section">
            <div class="client-box" style="width: 48%; float: left;">
                <div class="box-title">Facturé à</div>
                <div class="box-content">
                    <p><strong>Nom:</strong> {{ $order->customer_name ?? $order->user?->name ?? 'Non spécifié' }}</p>
                    <p><strong>Email:</strong> {{ $order->customer_email ?? $order->user?->email ?? 'Non spécifié' }}</p>
                    @if($order->customer_phone)
                    <p><strong>Tél:</strong> {{ $order->customer_phone }}</p>
                    @endif
                </div>
            </div>
            <div class="invoice-box" style="width: 48%; float: right;">
                <div class="box-title">Informations</div>
                <div class="box-content">
                    <p><strong>Statut:</strong> 
                        <span class="status-badge status-{{ $order->status }}">
                            @if($order->status === 'pending') En attente
                            @elseif($order->status === 'paid') Payée
                            @elseif($order->status === 'canceled') Annulée
                            @else {{ $order->status }}
                            @endif
                        </span>
                    </p>
                    <p><strong>Articles:</strong> {{ $order->items->count() }} article(s)</p>
                    <p><strong>Devise:</strong> {{ $order->currency }}</p>
                    <p><strong>Mode paiement:</strong> {{ $order->payment_method ?? 'Non spécifié' }}</p>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>
        
        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Description</th>
                    <th class="text-right" style="width: 16%;">Prix Unit. HT</th>
                    <th class="text-center" style="width: 12%;">Qté</th>
                    <th class="text-right" style="width: 22%;">Total HT</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->items as $item)
                <tr>
                    <td>
                        <div class="item-title">{{ $item->title }}</div>
                        @if($item->product_id)
                        <div class="item-ref">Réf: PRD-{{ $item->product_id }}</div>
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($item->price, 2, ',', ' ') }} {{ $order->currency }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right"><strong>{{ number_format($item->line_total, 2, ',', ' ') }} {{ $order->currency }}</strong></td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #9ca3af; padding: 20px;">Aucun article</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Totals Section -->
        <div class="totals-section">
            <div class="totals-left">
                @if($order->notes)
                <div class="notes-box">
                    <div class="notes-title">Notes / Conditions</div>
                    {{ $order->notes }}
                </div>
                @endif
            </div>
            <div class="totals-right">
                <table class="totals-table">
                    <tr>
                        <td class="label">Sous-total HT:</td>
                        <td class="value">{{ number_format($order->subtotal, 2, ',', ' ') }} {{ $order->currency }}</td>
                    </tr>
                    {{-- Si vous avez des taxes ou remises, ajoutez-les ici
                    <tr>
                        <td class="label">TVA (20%):</td>
                        <td class="value">{{ number_format($order->tax_amount ?? 0, 2, ',', ' ') }} {{ $order->currency }}</td>
                    </tr>
                    <tr>
                        <td class="label">Remise:</td>
                        <td class="value">- {{ number_format($order->discount ?? 0, 2, ',', ' ') }} {{ $order->currency }}</td>
                    </tr>
                    --}}
                    <tr class="total-line">
                        <td class="label">TOTAL TTC:</td>
                        <td class="value">{{ number_format($order->total, 2, ',', ' ') }} {{ $order->currency }}</td>
                    </tr>
                </table>
            </div>
            <div style="clear: both;"></div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><span class="footer-highlight">{{ $company['name'] ?? config('app.name') }}</span> | {{ $company['address'] ?? '123 Rue Example, Dakar' }}</p>
            <p>Email: {{ $company['email'] ?? 'contact@example.com' }} | Tél: {{ $company['phone'] ?? '+221 XX XXX XX XX' }}</p>
            <p style="margin-top: 5px;">Facture générée le {{ now()->format('d/m/Y à H:i') }} | Merci pour votre confiance</p>
        </div>
    </div>
</body>
</html>