<!DOCTYPE html>
<html lang="en">

<head>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Reddit Mono', monospace;
    }

    @page {
      size: 58mm 6in;
      margin-top: 0.4cm;
      margin-left: 0.4cm;
      margin-right: 0.4cm;
    }

    table {
      width: 100%;
    }

    tr {
      width: 100%;
    }

    h1 {
      text-align: center;
      vertical-align: middle;
    }

    #logo {
      width: 60%;
      text-align: center;
      padding: 5px;
      margin: 2px;
      display: block;
      margin: 0 auto;
    }

    header {
      width: 100%;
      text-align: center;
      vertical-align: middle;
    }

    .sub-header {
      text-align: center;
      font-size: 10px;
    }

    .bill-details td {
      font-size: 10px;
    }

    .items td {
      font-size: 10px;
      text-align: right;
      vertical-align: bottom;
    }

    p {
      padding: 1px;
      margin: 0;
    }

    footer {
      font-size: 12px;
    }
  </style>
</head>

<body>
  <header>
    @php
      $company = App\Models\Company::where('group_id', auth()->user()->id_group)->first();
    @endphp
    <img id="logo" class="logo" src="{{ asset('storage/logo/' . $company->logo) }}"></img>
  </header>
  <p class="sub-header">RINCIAN TRANSAKSI<br> PENGGUNAAN WIFI {{ $company->nickname }}<br><br>{{ date('d/m/Y H:i:s') }}
  </p><br>
  <table class="bill-details">
    <tbody>
      @foreach ($invoice as $item)
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
          $getitem = explode('|', $item->item);
          $paket = $getitem[1];
          $amount_ppn = ($item->price * $item->ppn) / 100;
          $amount_discount = ($item->price * $item->discount) / 100;
          $amount_total = $item->price + $amount_ppn - $amount_discount;
          $getperiod = date('Y-m-d', strtotime($item->period));
          $period = tgl_indo($getperiod);
        @endphp

        <tr>
          <td>ID Member</td>
          <td><span>: {{ $item->member->id_member }}</span></td>
        </tr>
        <tr>
          <td>Nama</td>
          <td><span>: {{ $item->member->full_name }}</span></td>
        </tr>
        <tr>
          <td>Tipe</td>
          <td><span>: {{ $item->payment_type }}</span></td>
        </tr>
        <br>
        <tr>
          <td>Invoice</td>
          <td><span>: {{ $item->no_invoice }}</span></td>
        </tr>
        <tr>
          <td>Periode</td>
          <td><span>: {{ $period }}</span></td>
        </tr>
        <tr>
          <td>Internet</td>
          <td><span>: {{ $paket }}</span></td>
        </tr>

        <tr>
          <td>Nominal</td>
          <td><span>: {{ number_format($item->price, 0, '.', '.') }}</span></td>
        </tr>
        @if ($item->ppn !== null && $item->ppn != 0)
          <tr>
            <td>PPN</td>
            <td><span>: {{ number_format($amount_ppn, 0, '.', '.') }}</span></td>
          </tr>
        @endif
        @if ($item->discount !== null && $item->discount != 0)
          <tr>
            <td>Discount</td>
            <td><span>: {{ number_format($amount_discount, 0, '.', '.') }}</span></td>
          </tr>
        @endif
        <tr>
          <td>Total</td>
          <td><span>: {{ number_format($amount_total, 0, '.', '.') }}</span></td>
        </tr>
        <tr>
          <td>Jatuh Tempo</td>
          <td><span>: {{ date('d/m/Y', strtotime($item->due_date)) }}</span></td>
        </tr>
        <tr>
          <td>Status</td>
          @if (!$item->status->isPaid())
            <td>: <span style="color: red">UNPAID</span></td>
          @else
            <td>: <span style="color: green">LUNAS</span></td>
          @endif
        </tr>
        <br>
        <tr>
          <td>Kasir</td>
          <td><span>: {{ auth()->user()->name }}</span></td>
        </tr>
        <br>
      @endforeach
    </tbody>
  </table>

  <section>
    <br><br>
    <p style="text-align:center;font-size:10px">
      Terima kasih
    </p>
  </section>
  <footer style="text-align:center">
    <p style="font-size:10px">Simpan resi ini sebagai bukti pembayaran yang sah</p><br>
    <p style="font-size:10px">Informasi Hubungi<br>WhatsApp {{ $company->wa }}</p><br>
  </footer>
</body>

</html>
