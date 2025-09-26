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
$nominal_discount = $invoice->discount;
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
        @media print {
    body {
        margin: 0;
        padding: 0;
        -webkit-print-color-adjust: exact;
    }

    .page-break {
        page-break-before: always;
    }

    header, section, .footer {
        page-break-inside: avoid;
    }
}

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            font-size: 14px;
            color: #1f2937;
            background-color: #fff;
            padding: 40px;
            max-width: 800px;
            margin: auto;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
        }

        .company-info {
            line-height: 1.6;
            max-width: 60%;
        }

        .company-info img {
            height: 80px; /* Logo diperbesar */
            margin-bottom: 10px;
        }

        .invoice-info {
            text-align: right;
            line-height: 1.6;
        }

        .invoice-info h1 {
            font-size: 24px;
            margin-bottom: 6px;
        }

        section {
            margin-bottom: 30px;
        }

        .section-title {
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        th {
            background-color: #f9fafb;
        }

        .text-right {
            text-align: right;
        }

        .totals-table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px 10px;
            border: none;
        }

        .totals-table .label {
            width: 65%;
        }

        .note {
            margin-top: 40px;
            font-size: 13px;
            color: #6b7280;
            text-align: center;
        }

        .pay-info {
            margin-top: 20px;
        }

        .qr-code {
            margin-top: 50px;
            text-align: center;
        }

        .qr-code img {
            height: 120px;
        }

        .footer {
            margin-top: 60px;
            text-align: center;
            font-weight: 600;
            font-size: 15px;
            color: #111827;
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
            <div>Status: <span style="color: {{ $invoice->status === 'unpaid' ? '#dc2626' : '#16a34a' }}">{{ strtoupper($invoice->status) }}</span></div>
        </div>
    </header>

    <section>
        <div class="section-title">Pelanggan</div>
        <div>
            <strong>{{ $invoice->rpppoe->full_name }}</strong> ({{ $invoice->rpppoe->kode_area }})<br>
            ID Pelanggan: {{ $invoice->rpppoe->id_pelanggan }}<br>
            {{ $invoice->rpppoe->address }}<br>
            Kontak: {{ $invoice->rpppoe->wa }}
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
                    <td>{{ $invoice->item }} <br>{{$invoice->subscribe}}</td>
                    <td>{{ $periode_format }}</td>
                    <td class="text-right">Rp {{ $amount_format }}</td>
                </tr>
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
                <td class="label text-right">Subtotal:</td>
                <td class="text-right">Rp {{ $amount_format }}</td>
            </tr>
            @if ($invoice->ppn)
            <tr>
                <td class="label text-right">PPN ({{ $invoice->ppn }}%):</td>
                <td class="text-right">Rp {{ $ppn_format }}</td>
            </tr>
            @endif
            @if ($invoice->discount)
            <tr>
                <td class="label text-right">Diskon :</td>
                <td class="text-right">Rp {{ $discount_format }}</td>
            </tr>
            @endif
            <tr>
                <td class="label text-right"><strong>Total:</strong></td>
                <td class="text-right"><strong>Rp {{ $total_format }}</strong></td>
            </tr>
        </table>
    </section>

    <section class="pay-info">
        <div class="section-title">Metode Pembayaran</div>
        <div>
            Transfer ke: <strong>{{ $company->bank }}</strong><br>
            a.n <strong>{{ $company->holder }}</strong>
        </div>
    </section>

    <div class="qr-code">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($invoice->payment_url) }}" alt="QR Code">
        <div class="small">Scan untuk bayar online</div>
    </div>

    <div class="note">
        {{ $company->note }}
    </div>

    <div class="footer">
        TERIMA KASIH
    </div>

</body>
</html>


@endforeach
