@php
use Carbon\Carbon;
use Carbon\CarbonInterval;
Carbon::setLocale('id');

$nickname = \App\Models\Setting\Company::where('shortname', multi_auth()->shortname)
                ->value('singkatan') ?: 'RADIUSQU';
$whatsapp = \App\Models\Setting\Company::where('shortname', multi_auth()->shortname)
                ->value('wa') ?: '';
$i        = 1;
@endphp

@foreach ($user as $item)
    @php
        $rprofile = $item->rprofile;

        // Hitung masa aktif
        $validity = $rprofile->validity === 'Unlimited'
            ? 'Unlimited'
            : ((int)$rprofile->validity >= 2592000
                ? CarbonInterval::seconds((int)$rprofile->validity)->cascade()->forHumans(['skip' => ['day','hour','minute','second']])
                : CarbonInterval::seconds((int)$rprofile->validity)->cascade()->forHumans());

        // Hitung kuota
        $quota = $rprofile->quota === 'Unlimited'
            ? 'Unlimited'
            : (function() use ($rprofile) {
                $precision  = 2;
                $quotaValue = (int)$rprofile->quota;
                $base       = log($quotaValue, 1024);
                $suffixes   = [' bytes', ' KB', ' MB', ' GB', ' TB'];
                return round(pow(1024, $base - floor($base)), $precision) . $suffixes[(int) floor($base)];
            })();

        // Format harga
        $priceValue = $rprofile->price;
        $price = $priceValue == 0
            ? 0
            : number_format($priceValue, 0, '.', '.');

        // Tentukan warna untuk teks nickname dan background pesan "JANGAN DIBUANG SELAMA MASIH AKTIF" berdasarkan harga
        if ($priceValue == 0) {
    $nicknameColor     = '#333';       // warna teks untuk nickname
    $messageBackground = '#333';       // background pesan
} elseif ($priceValue < 2000) {
    $nicknameColor     = '#6c757d';    // abu-abu
    $messageBackground = '#6c757d';
} elseif ($priceValue < 3000) {
    $nicknameColor     = '#007bff';    // biru muda
    $messageBackground = '#007bff';
} elseif ($priceValue < 5000) {
    $nicknameColor     = '#6610f2';    // ungu
    $messageBackground = '#6610f2';
} elseif ($priceValue < 7000) {
    $nicknameColor     = '#17a2b8';    // cyan
    $messageBackground = '#17a2b8';
} elseif ($priceValue < 10000) {
    $nicknameColor     = '#20c997';    // hijau toska
    $messageBackground = '#20c997';
} elseif ($priceValue < 15000) {
    $nicknameColor     = '#28a745';    // hijau
    $messageBackground = '#28a745';
} elseif ($priceValue < 20000) {
    $nicknameColor     = '#ffc107';    // kuning
    $messageBackground = '#ffc107';
} elseif ($priceValue < 30000) {
    $nicknameColor     = '#fd7e14';    // oranye
    $messageBackground = '#fd7e14';
} elseif ($priceValue < 50000) {
    $nicknameColor     = '#dc3545';    // merah
    $messageBackground = '#dc3545';
} else {
    $nicknameColor     = '#b21f2d';    // merah gelap
    $messageBackground = '#b21f2d';
}

    @endphp

    <!DOCTYPE HTML>
    <html>
    <head>
        <title>{{ $item->created_at }}</title>
        <style type="text/css">
            @import url('https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap');
            body { font-family: 'Noto Sans'; }
            /* Definisi style tambahan jika diperlukan */
        </style>
    </head>

    <body>
        <table style="display: inline-block; border-collapse: collapse; border: 1px solid #444; margin: 2.5px; width: 180px; overflow: hidden; position: relative; padding: 1px; margin-right: 6px; margin-bottom: 3px;">
            <tbody>
                <tr>
                    <td style="background: transparent; color: #666; padding: 0;" valign="top" colspan="2">
                        <div style="text-align: center; color: #fff; font-size: 10px; font-weight: bold; margin: 1px; padding: 2.5px;"></div>
                    </td>
                </tr>
                <tr>
                    <td style="color: #666;" valign="top">
                        <table style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td style="width: 75px">
                                        <div style="position: relative; z-index: -1; padding: 0; float: left;">
                                            <div style="position: absolute; top: 0; display: inline; margin-top: -100px; width: 0; height: 0; border-top: 230px solid transparent; border-left: 50px solid transparent; border-right: 140px solid #DCDCDC;"></div>
                                        </div>
                                        <!-- Gunakan variabel $nicknameColor untuk teks nickname -->
                                        <h1 style="margin: -12px 0 0 0; font-size: 14px; color: {{ $nicknameColor }}">{{ $nickname }}</h1>
                                    </td>
                                    <td style="width: 115px">
                                        <div style="float: right; margin-top: 13px; width: 5%; text-align: right; font-size: 7px;"></div>
                                        <div style="margin: -10px; text-align: right; font-weight: bold; font-size: 14px; padding-left: 17px">
                                            <small style="font-size: 8px; margin-left: -12px; margin-top:2px; position: absolute">Rp</small>
                                            <b>{{ $price }}</b>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="color: #666; border-collapse: collapse;" valign="top">
                        @if ($item->username !== $item->value)
                            <table style="width: 100%; border-collapse: collapse;margin-top: -5px;">
                                <tbody>
                                    <tr>
                                        <td style="width: 95px" valign="top">
                                            <div style="clear: both; color: #555; margin-top: 3px; margin-bottom: 1px;">
                                                <div style="padding: 2px; border-bottom: 1px solid; text-align: left; font-weight: bold; font-size: 9px; color: #555">
                                                    USERNAME
                                                </div>
                                                <div style="padding: 2px; text-align: left; font-weight: bold; font-size: 10px; color: #222">
                                                    {{ $item->username }}
                                                </div>
                                            </div>
                                        </td>
                                        <td style="width: 95px" valign="top">
                                            <div style="clear: both; color: #555; margin-top: 3px; margin-bottom: 1px;">
                                                <div style="padding: 2px; border-bottom: 1px solid; text-align: left; font-weight: bold; font-size: 9px; color: #555">
                                                    PASSWORD
                                                </div>
                                                <div style="padding: 2px; text-align: left; font-weight: bold; font-size: 10px; color: #222">
                                                    {{ $item->value }}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width:100px; text-align:left;">
                                            <div style="text-align:left; color: #222; font-size:7px; margin:0; padding:2.5px;">
                                                <span style="display:inline-block; width:18px;">Aktif</span>: {{ Illuminate\Support\Str::title($validity) }}
                                            </div>
                                        </td>
                                        <td style="width:100px; text-align:left;">
                                            <div style="text-align:left; color: #222; font-size:7px; margin:0; padding:2.5px;">
                                                <span style="display:inline-block; width:22px;">Kuota</span>: {{ $quota }}
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            <!-- Tambahkan jarak margin-top untuk memisahkan nickname dan kode voucher -->
                            <table style="width:100%; border-collapse: collapse; margin-top: -5px;">
                                <tbody>
                                    <tr>
                                        <td style="width:95px" valign="top">
                                            <div style="clear: both; color: #555; margin-top: 0; margin-bottom: 2.5px;">
                                                <div style="padding:2px; border-bottom:1px solid; text-align:left; font-weight:bold; font-size:7px; color:#555">
                                                    KODE VOUCHER
                                                </div>
                                                <div style="padding:2px; text-align:left; font-weight:bold; font-size:14px; color:#222">
                                                    {{ $item->username }}
                                                </div>
                                            </div>
                                        </td>
                                        <td style="width:100px; text-align:left;">
                                            <div style="text-align:left; color:#222; font-size:7px; margin:0; padding:2.5px;">
                                                <span style="display:inline-block; width:25px; margin-left:10px;">Aktif</span>: {{ Illuminate\Support\Str::title($validity) }}<br>
                                                <span style="display:inline-block; width:25px; margin-left:10px;">Kuota</span>: {{ $quota }}<br>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        @endif
                        <table style="width:100%; border-collapse: collapse; margin-top:5px;">
                            <tbody>
                                <tr>
                                    <!-- Gunakan variabel $messageBackground untuk background pesan -->
                                    <td style="background: {{ $messageBackground }}; padding:0;" valign="top" colspan="1">
                                        <div style="text-align:left; font-size:6px; font-weight:bold; margin:0; padding:2.5px; color:#fff;">
                                            <b>&nbsp;INFORMASI HUBUNGI {{ $whatsapp }} </b>
                                        </div>
                                    </td>
                                    <td style="background: {{ $messageBackground }}; padding:0;" valign="top" colspan="1">
                                        <div style="text-align:left; font-size:6px; font-weight:bold; margin:0; padding:2.5px; color:#fff;">
                                            <b>[{{ $i++ }}]</b>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
    </html>
@endforeach
