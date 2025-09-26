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
        body {
            font-family: monospace;
            font-size: 11px;
            width: 220px; /* 58mm */
            margin: 0 auto;
            color: #000;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .small {
            font-size: 10px;
        }

        .mt-1 { margin-top: 4px; }
        .mt-2 { margin-top: 8px; }
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }

        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .divider-thick {
            border-top: 2px solid #000;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
            padding: 2px 0;
        }

        .logo {
            max-height: 40px;
            margin-bottom: 4px;
        }

        .total {
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="text-center mb-2">
        @if($company->logo)
            <img src="/storage/logo/{{ $company->logo }}" class="logo" alt="Logo">
        @endif
        <div class="bold">{{ strtoupper($company->name) }}</div>
        <div class="small">{{ $company->address }}</div>
        <div class="small">WA: {{ $company->wa }}</div>
    </div>

    <div class="divider"></div>

    <!-- Invoice Info -->
    <table>
        <tr>
            <td>Invoice</td>
            <td class="text-right">#{{ $invoice->no_invoice }}</td>
        </tr>
        <tr>
            <td>Jatuh Tempo</td>
            <td class="text-right">{{ $due_date_format }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td class="text-right">{{ strtoupper($invoice->status) }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Pelanggan -->
    <div class="bold mb-1">Pelanggan</div>
    <div>
        {{ $invoice->rpppoe->full_name }} ({{ $invoice->rpppoe->kode_area }})<br>
        ID: {{ $invoice->rpppoe->id_pelanggan }}
    </div>

    <div class="divider"></div>

    <!-- Detail Tagihan -->
    <div class="bold mb-1">Detail</div>
    <table>
        <tr>
            <td colspan="2">{{ $invoice->item }}</td>
        </tr>
        <tr>
            <td>Periode</td>
            <td class="text-right">{{ $periode_format }}</td>
        </tr>
        <tr>
            <td>Harga</td>
            <td class="text-right">Rp {{ $amount_format }}</td>
        </tr>
        @if ($invoice->ppn)
        <tr>
            <td>PPN ({{ $invoice->ppn }}%)</td>
            <td class="text-right">Rp {{ $ppn_format }}</td>
        </tr>
        @endif
        @if ($invoice->discount)
        <tr>
            <td>Diskon</td>
            <td class="text-right">Rp {{ $discount_format }}</td>
        </tr>
        @endif
        <tr class="total">
            <td>Total</td>
            <td class="text-right">Rp {{ $total_format }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Metode Pembayaran -->
    <div class="bold mb-1">Pembayaran</div>
    <div>
        {{ $company->bank }}<br>
        a.n {{ $company->holder }}
    </div>

    <div class="divider"></div>

    <!-- Catatan -->
    <div class="text-center small mt-2">
        {{ $company->note }}
    </div>

    <!-- QR Code -->
    <div class="text-center mt-2">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($invoice->payment_url) }}" alt="QR Code">
    </div>

    <div class="divider-thick"></div>

    <div class="text-center bold">
        TERIMA KASIH
    </div>
</body>
</html>



@endforeach
