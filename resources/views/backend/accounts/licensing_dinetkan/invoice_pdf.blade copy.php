<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 20mm;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fc;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 1200px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #f1f3f7;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }
        .badge {
            background: #eceff4;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: bold;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
        }
        .info-box {
            width: 48%;
        }
        .table-container {
            margin-top: 20px;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .table th {
            background: #f9fafb;
            font-weight: bold;
        }
        .summary, .timeline {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .summary strong {
            font-size: 18px;
        }
        .timeline ul {
            padding: 0;
            list-style: none;
        }
        .timeline li::before {
            content: '\2022';
            color: #6366f1;
            font-weight: bold;
            display: inline-block; 
            width: 1em;
            margin-left: -1em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Invoice #{{ $invoice->no_invoice }}</h2>
            
            @if ($invoice->status->value === 1)
              <span class="badge">Unpaid</span>
            @else
              <span class="badge">Paid</span>
            @endif
        </div>
        <p>Issued date: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d.m.Y') }}</p>
        <div class="info-section">
            <div class="info-box">
                <h3>From:</h3>
                <strong>{{ $settings->name }}</strong><br>
                {{ $settings->address }}
            </div>
            <div class="info-box">
                <h3>To:</h3>
                <strong>{{ $invoice->admin->name }}</strong><br>
                {{ $invoice->admin->address }}
            </div>
        </div>
        <div class="table-container">
            <h3>Items</h3>
            <table class="table">
                <tr>
                    <th>ITEM</th>
                    <th>PRICE</th>
                </tr>
                <tr>
                    <td>Registrasi : {{ $invoice->item }}</td>
                    <td>Rp{{ number_format($invoice->price, 0, '.', '.') }}</td>
                </tr>
                @if ($priceData->ppn > 0)
                <tr>
                    <td>PPN ({{ $priceData->ppnPercentage }}%)</td>
                    <td>Rp{{ number_format($priceData->ppn, 0, '.', '.') }}</td>
                </tr>
                @endif
                @if ($priceData->price_otc > 0)
                <tr>
                    <td>Service : {{ $invoice->item }}</td>
                    <td>Rp{{ number_format($invoice->price_otc, 0, '.', '.') }}</td>
                </tr>
                @endif
                @if ($priceData->ppn_otc > 0)
                <tr>
                    <td>PPN ({{ $invoice->ppn_otc }}%)</td>
                    <td>Rp{{ number_format( ($invoice->price_otc * $invoice->ppn_otc/ 100)  , 0, '.', '.') }}</td>
                </tr>
                @endif
                @if ($priceData->discount > 0)
                <tr>
                    <td>Discount ({{ $priceData->discountPercentage }}%)</td>
                    <td>-Rp{{ number_format($priceData->discount, 0, '.', '.') }}</td>
                </tr>
                @endif
                @if ($priceData->discountCoupon > 0)
                <tr>
                    <td>Discount Coupon</td>
                    <td>-Rp{{ number_format($priceData->discountCoupon, 0, '.', '.') }}</td>
                </tr>
                @endif
                @if ($priceData->adminFee > 0)
                <tr>
                    <td>Admin Fee</td>
                    <td>Rp{{ number_format($priceData->adminFee, 0, '.', '.') }}</td>
                </tr>
                @endif
                <tr>
                    <td style="font-weight: bold;">Total incl. VAT:</td>
                    <td style="font-weight: bold;">Rp{{ number_format($priceData->total, 0, '.', '.') }}</td>
                </tr>
            </table>
        </div>
        <!-- <div class="summary">
            <h3>Summary</h3>
            <p>Payment: Rp1.000.000</p>
            <p>Payment method fee: Rp0</p>
            <strong>Total Charge: Rp1.000.000</strong>
        </div>
        <div class="timeline">
            <h3>Timeline</h3>
            <ul>
                <li>Invoice created - 16.03.2025 @ 12:00 AM</li>
            </ul>
        </div> -->
    </div>
</body>
</html>
