@php
    function tgl_indo($tanggal)
    {
        $bulan = [
            1 => 'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        ];
        $pecahkan = explode('-', $tanggal);
        return $bulan[(int) $pecahkan[1]] . ' ' . $pecahkan[0];
    }
@endphp

@foreach ($invoices as $invoice)

@php
if ($invoice->payment_type === 'Prabayar' && $invoice->billing_period === 'Fixed Date') {
    $periode = \Carbon\Carbon::createFromFormat('Y-m-d', $invoice->due_date);
    $get_periode = date('Y-m-d', strtotime($invoice->period));
    $periode_format = tgl_indo($get_periode);
} elseif ($invoice->payment_type === 'Pascabayar' && $invoice->billing_period === 'Fixed Date') {
    $periode = \Carbon\Carbon::createFromFormat('Y-m-d', $invoice->due_date)->subMonthsWithNoOverflow(1);
    $get_periode = date('Y-m-d', strtotime($periode));
    $periode_format = tgl_indo($get_periode);
} elseif ($invoice->payment_type === 'Pascabayar' && $invoice->billing_period === 'Billing Cycle') {
    $periode = \Carbon\Carbon::createFromFormat('Y-m-d', $invoice->due_date)->subMonthsWithNoOverflow(1);
    $get_periode = date('Y-m-d', strtotime($periode));
    $periode_format = tgl_indo($get_periode);
}
$nominal_ppn = ($invoice->price * $invoice->ppn) / 100;
$nominal_discount = ($invoice->price * $invoice->discount) / 100;
$payment_total = $invoice->price + $nominal_ppn - $nominal_discount;
$amount_format = number_format($invoice->price, 0, '.', '.');
$total_format = number_format($payment_total, 0, '.', '.');
$due_date_format = date('d/m/Y', strtotime($invoice->due_date));
$ppn_format = number_format($nominal_ppn, 0, '.', '.');
$discount_format = number_format($nominal_discount, 0, '.', '.');

@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->no_invoice }}</title>
    <style>
      @page {
            size: 14cm 22cm; /* Set page size to 14x22 cm */
            margin: 5mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            font-size: 11px;
            color: #1f2937;
            background-color: #fff;
            padding: 10px;
            margin: 0;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .company-info {
            line-height: 1.4;
            max-width: 58%;
        }

        .company-info img {
            height: 50px;
            margin-bottom: 5px;
        }

        .invoice-info {
            text-align: right;
            font-size: 10px;
            line-height: 1.4;
        }

        .invoice-info h1 {
            font-size: 16px;
            margin-bottom: 4px;
        }

        section {
            margin-bottom: 10px;
        }

        .section-title {
            font-weight: 600;
            margin-bottom: 4px;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        th, td {
            padding: 6px 5px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        th {
            background-color: #f9fafb;
            font-size: 11px;
        }

        .text-right {
            text-align: right;
        }

        tfoot td {
            background-color: #f9fafb;
            font-weight: 500;
            font-size: 11px;
        }

        tfoot tr.total td {
            font-size: 12px;
            font-weight: bold;
        }

        .note {
            margin-top: 14px;
            font-size: 10px;
            color: #374151;
            text-align: center;
            line-height: 1.4;
        }

        .payment-flex {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-top: 12px;
        }

        .payment-info {
            flex: 1;
            font-size: 11px;
        }

        .qr-code {
            text-align: right;
            font-size: 10px;
        }

        .qr-code img {
            height: 90px;
            margin-bottom: 4px;
        }

        .qr-caption {
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>
<body>

<header>
    <div class="company-info">
        @if($company->logo)
            <img src="/storage/logo/{{ $company->logo }}" alt="Logo Perusahaan">
        @endif
        <br>
        <strong>{{ $company->name }}</strong><br>
        {{ $company->address }}<br>
        WA: {{ $company->wa }} | Email: {{ $company->email }}
    </div>
    <div class="invoice-info">
        <h1>INVOICE</h1>
        <div>#{{ $invoice->no_invoice }}</div>
        <div>Jatuh Tempo: {{ $due_date_format }}</div>
        <div>Status:
            <span style="color: {{ $invoice->status === 'unpaid' ? '#dc2626' : '#16a34a' }}">
                {{ strtoupper($invoice->status) }}
            </span>
        </div>
    </div>
</header>

<section>
    <div class="section-title">Pelanggan</div>
    <div>
        <strong>{{ $invoice->rpppoe->full_name }}</strong> ({{ $invoice->rpppoe->kode_area }})<br>
        ID: {{ $invoice->rpppoe->id_pelanggan }} | {{ $invoice->rpppoe->wa }}<br>
        {{ $invoice->rpppoe->address }}
    </div>
</section>

<section>
    <div class="section-title">Detail Tagihan</div>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Periode</th>
                <th class="text-right">Harga</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $invoice->item }}</td>
                <td>{{ $periode_format }}</td>
                <td class="text-right">Rp {{ $amount_format }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td class="text-right">Subtotal:</td>
                <td class="text-right">Rp {{ $amount_format }}</td>
            </tr>
            @if ($invoice->ppn)
            <tr>
                <td></td>
                <td class="text-right">PPN ({{ $invoice->ppn }}%):</td>
                <td class="text-right">Rp {{ $ppn_format }}</td>
            </tr>
            @endif
            @if ($invoice->discount)
            <tr>
                <td></td>
                <td class="text-right">Diskon ({{ $invoice->discount }}%):</td>
                <td class="text-right">Rp {{ $discount_format }}</td>
            </tr>
            @endif
            <tr class="total">
                <td></td>
                <td class="text-right">Total:</td>
                <td class="text-right">Rp {{ $total_format }}</td>
            </tr>
        </tfoot>
    </table>
</section>

<div class="payment-flex">
    <div class="payment-info">
        <div class="section-title">Pembayaran</div>
        <div>
            Transfer ke: <strong>{{ $company->bank }}</strong><br>
            a.n <strong>{{ $company->holder }}</strong>
        </div>
    </div>
    <div class="qr-code">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($invoice->payment_url) }}" alt="QR Code">
        <div class="qr-caption">Scan untuk bayar online</div>
    </div>
</div>

<div class="note">
    {{ $company->note }}
</div>

</body>
</html>


@endforeach
