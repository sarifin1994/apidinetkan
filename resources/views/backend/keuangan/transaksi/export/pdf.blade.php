<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan {{$company->name}} - {{$periode}}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 30px;
            padding: 20px;
            background-color: #ffffff;
            color: #333;
            position: relative;
        }
        h2 {
            text-align: center;
            color: #222;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            width: 300px;
            height: auto;
            z-index: -1;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 5px 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        th, td {
            padding: 10px;
            text-align: right;
            border: 1px solid #ccc;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: left;
        }
        .left {
            text-align: left;
        }
        .total {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        .saldo {
            font-weight: bold;
            background-color: #e0f7fa;
        }
    </style>
</head>
<body>

    <h2>Laporan Keuangan {{$company->name}}</h2>

    <!-- Informasi Periode -->
    <table class="info-table">
        <tr>
            <td class="left"><strong>Periode Laporan</strong></td>
            <td class="left">{{ $periode }}</td>
        </tr>
        <tr>
            <td class="left"><strong>Tanggal Cetak</strong></td>
            <td class="left">{{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</td>
        </tr>
        <tr>
            <td class="left"><strong>Dicetak Oleh</strong></td>
            <td class="left">{{ multi_auth()->username }}</td>
        </tr>
    </table>

    <!-- Tabel Laporan Keuangan -->
    <table>
        <thead>
            <tr>
                <th class="left">Tanggal</th>
                <th class="left">Kategori</th>
                <th class="left">Deskripsi</th>
                <th>Debet (Rp)</th>
                <th>Kredit (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi as $data)
            <tr>
                <td class="left">{{ date("d/m/Y H:i:s", strtotime($data->tanggal)) }}</td>
                <td class="left">{{ $data->kategori }}</td>
                <td class="left">{{ $data->deskripsi }}</td>
                @if($data->tipe === 'Pemasukan')
                <td>{{ number_format(floatval($data->nominal), 0, ',', '.') }}</td>
                <td>-</td>
                @elseif($data->tipe === 'Pengeluaran')
                <td>-</td>
                <td>{{ number_format(floatval($data->nominal), 0, ',', '.') }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="3" class="left">Total Pemasukan</td>
                <td>{{ number_format($totalpemasukan, 0, ',', '.') }}</td>
                <td>-</td>
            </tr>
            <tr class="total">
                <td colspan="3" class="left">Total Pengeluaran</td>
                <td>-</td>
                <td>{{ number_format($totalpengeluaran, 0, ',', '.') }}</td>
            </tr>
            <tr class="saldo">
                <td colspan="3" class="left">Saldo Akhir</td>
                <td colspan="2">{{ number_format($totalpemasukan - $totalpengeluaran, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
