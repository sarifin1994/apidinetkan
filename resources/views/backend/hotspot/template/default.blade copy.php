@php
$remark = $user[0]['remark'];
$profile = $user[0]['rprofile']['name'];
$nickname = \App\Models\Setting\Company::where('shortname',multi_auth()->shortname)->select('singkatan')->first()->singkatan;
$i = 1;  
@endphp
@foreach ($user as $item)
    @php
        Carbon\Carbon::setLocale('id');
        if ($item->rprofile->validity === 'Unlimited') {
            $validity = 'Unlimited';
        } else {
            if($item->rprofile->validity >= 2592000){
            $validity = Carbon\CarbonInterval::seconds((int)$item->rprofile->validity)
                ->cascade()
                ->forHumans([   
                    'skip'        => ['day','hour','minute','second'],
                ]);
            }else{
                $validity = Carbon\CarbonInterval::seconds((int)$item->rprofile->validity)
                ->cascade()
                ->forHumans();
            }
        }

        $precision = 2;

        if ($item->rprofile->quota === 'Unlimited') {
            $quota = 'Unlimited';
        } else {
            $base = log($item->rprofile->quota) / log(1024);
            $suffixes = [' bytes', ' KB', ' MB', ' GB', ' TB'];
            $quota = round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
        }

        if ($item->rprofile->price === 0) {
            $price = 0;
        } else {
            $price = number_format($item->rprofile->price, 0, '.', '.');
        }
    @endphp
    <!DOCTYPE HTML>
    <head>
        <title>{{$item->created_at}}</title>
        <style type="text/css">
@import url('https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap');            body {
                font-family: 'Noto Sans';
            }

            /* .qrcode {
                width: 50px;
                height: 50px;
                margin-top: -1px;
                margin-left: 10px;
                padding: 2px;
                border: 1px solid #444;
                border-radius: 4px;
                background-color: #fff
            } */
        </style>

    </head>

    <body>
        <table
            style="display: inline-block;border-collapse: collapse;border: 1px solid #666;margin: 2.5px;width: 160px;overflow:hidden;position:relative;padding: 1px;margin: 0px;border: 1px solid #444; background:;margin-right: 6px;margin-bottom:3px;">
            <tbody>
                <tr>
                    <td style="background:transparent;color:#666;padding:0px;" valign="top" colspan="2">
                        <div
                            style="text-align:center;color:#fff;font-size:10px;font-weight:bold;margin:1px;padding:2.5px;">

                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="color:#666;" valign="top">
                        <table style="width:100%;">
                            <tbody>
                                <tr>
                                <tr>
                                    <td style="width:75px">
                                        <div style="position:relative;z-index:-1;padding: 0px;float:left;">
                                            <div
                                                style="position:absolute;top:0;display:inline;margin-top:-100px;width: 0; height: 0; border-top: 230px solid transparent;border-left: 50px solid transparent;border-right:140px solid #DCDCDC; ">
                                            </div>

                                        </div>
                                        {{-- <img style="margin:-15px 0 0 1px;" width="85" height="20"
                                        src="https://cdn.bizfiber.net/wp-content/uploads/2022/10/logo-bizfiber.jpg"
                                        alt="logo"> --}}
                                        <h1 style="margin:-18px 0 0 0;font-size:14px;color:#fe0e01">{{ $nickname }}
                                            {{-- <span style="color:#111">NET</span> --}}
                                        </h1>
                                    </td>
                                    <td style="width:115px">
                                        <div
                                            style="float:right;margin-top:13px;margin-right:0px;width:5%;text-align:right;font-size:7px;">
                                        </div>
                                        <div class="warnaharga"
                                            style="margin:-10px;text-align:right;font-weight:bold;font-size:12px;padding-left:17px">
                                            <small style="font-size:8px;margin-left:-15px;position:absolute">
                                                Rp.
                                            </small>
                                            <b id="hargaku" class="warnaharga">{{ $price }}</b>
                                        </div>
                                    </td>
                                </tr>
                </tr>
            </tbody>
        </table>
        </td>
        </tr>
        <tr>
            <td style="color:#666;border-collapse: collapse;" valign="top">
                @if ($item->username !== $item->value)
                    <table style="width:100%;border-collapse: collapse;">
                        <tbody>
                            <tr>
                                <td style="width:95px"valign="top">
                                    <div style="clear:both;color:#555;margin-top:3px;margin-bottom:1px;">
                                        <div
                                            style="padding:2px;border-bottom:1px solid;text-align:left;font-weight:bold;font-size:9px;color:#555">
                                            USERNAME</div>
                                        <div class="warnaharga"
                                            style="padding:2px;text-align:left;font-weight:bold;font-size:10px;color:#222">
                                            {{ $item->username }}
                                        </div>
                                    </div>
                                </td>
                                <td style="width:95px"valign="top">
                                    <div style="clear:both;color:#555;margin-top:3px;margin-bottom:1px;">
                                        <div
                                            style="padding:2px;border-bottom:1px solid;text-align:left;font-weight:bold;font-size:9px;color:#555">
                                            PASSWORD</div>
                                        <div class="warnaharga"
                                            style="padding:2px;text-align:left;font-weight:bold;font-size:10px;color:#222">
                                            {{ $item->value }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <p style="margin-top:-10px;margin-bottom:0px"></p>
                            <tr>
                                <td style="width:100px;text-align:left;">
                                    <div style="text-align:left;color:#222;font-size:7px;margin:0px;padding:2.5px;">
                                       Aktif : {{ Illuminate\Support\Str::title($validity) }}
                                    </div>
                                </td>
                                <td style="width:100px;text-align:left;">
                                    <div style="text-align:left;color:#222;font-size:7px;margin:0px;padding:2.5px;">
                                        Kuota : {{ $quota }}
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                @else
                <table style="width:100%;border-collapse: collapse;">
                    <tbody>
                        <tr>
                            <td style="width:95px"valign="top">
                                <div style="clear:both;color:#555;margin-top:0px;margin-bottom:2.5px;">
                                    <div
                                        style="padding:2px;border-bottom:1px solid;text-align:left;font-weight:bold;font-size:7px;color:#555">
                                        KODE VOUCHER</div>
                                    <div class="warnaharga"
                                        style="padding:2px;text-align:left;font-weight:bold;font-size:14px;color:#222">
                                        {{ $item->username }}
                                    </div>
                                </div>
                            </td>
                            <p style="margin-top:-10px;margin-bottom:0px"></p>
                            <td style="width:100px;text-align:right;">
                                <div style="text-align:right;color:#222;font-size:7px;margin:0px;padding:2.5px;">
                                    Aktif : {{ Illuminate\Support\Str::title($validity) }}<br>
                                    Kuota : {{ $quota }}<br>
                                </div>

                            </td>
                        </tr>
                    </tbody>
                </table>
                @endif
                <table style="width:100%;border-collapse: collapse;">
                    <tbody>
                        <tr>
                        </tr>
                        <tr>
                            <td class="warnalist" style="color:#666;padding:0px;background:#fe0e01" valign="top"
                                colspan="1">
                                <div
                                    style="text-align:left;color:#fff;font-size:6px;font-weight:bold;margin:0px;padding:2.5px;">
                                    <b>&nbspJANGAN DIBUANG SELAMA MASIH AKTIF</b>
                                </div>
                            </td>
                            <td class="warnalist" style="color:#666;padding:0px;background:#fe0e01" valign="top"
                                colspan="1">
                                <div
                                    style="text-align:right;color:#fff;font-size:6px;font-weight:bold;margin:0px;padding:2.5px;">
                                    <b>[{{$i++}}]</b>
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
