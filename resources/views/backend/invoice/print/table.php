<table width="100%" cellpadding="0" cellspacing="0" align="center" class="fullTable">
    <tr>
        <td height="20"></td>
    </tr>
    <tr>
        <td>
            <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable"
                style="border-radius: 10px 10px 0 0;">
                <tr class="hiddenMobile">
                    <td height="40"></td>
                </tr>
                <tr class="visibleMobile">
                    <td height="30"></td>
                </tr>

                <tr>
                    <td>
                        <table width="600" border="0" cellpadding="0" cellspacing="0" align="center"
                            class="fullPadding">
                            <tbody>
                                <tr>
                                    <td>
                                        <table width="220" border="0" cellpadding="0" cellspacing="0"
                                            align="left" class="col">
                                            <tbody>
                                                <tr>
                                                    <td align="left"> <img
                                                            src="http://www.supah.it/dribbble/017/logo.png"
                                                            width="32" height="32" alt="logo"
                                                            border="0" /></td>
                                                </tr>
                                                <tr class="hiddenMobile">
                                                    <td height="40"></td>
                                                </tr>
                                                <tr class="visibleMobile">
                                                    <td height="20"></td>
                                                </tr>
                                                <tr>
                                                    <td
                                                        style="font-size: 18px; color: #5b5b5b; font-family: 'Open Sans', sans-serif; line-height: 18px; vertical-align: top; text-align: left;">
                                                        <b>{{ $invoice->rpppoe->full_name }} /
                                                            {{ $invoice->rpppoe->id_pelanggan }}</b><br>
                                                        <br> <small
                                                            style="font-size:14px;margin">{{ $invoice->rpppoe->address }}</small>
                                                        <br> <small
                                                            style="font-size:14px">{{ $invoice->rpppoe->wa }}</small>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table width="220" border="0" cellpadding="0" cellspacing="0"
                                            align="right" class="col">
                                            <tbody>
                                                <tr class="visibleMobile">
                                                    <td height="20"></td>
                                                </tr>
                                                <tr>
                                                    <td height="5"></td>
                                                </tr>
                                                <tr>
                                                    <td
                                                        style="font-size: 28px; color: #1f01cc; letter-spacing: -1px; font-family: 'Open Sans', sans-serif; line-height: 1; vertical-align: top; text-align: right;">
                                                        Invoice
                                                    </td>
                                                </tr>
                                                <tr>
                                                <tr class="hiddenMobile">
                                                    <td height="50"></td>
                                                </tr>
                                                <tr class="visibleMobile">
                                                    <td height="20"></td>
                                                </tr>
                                                <tr>
                                                    <td
                                                        style="font-size: 18px; color: #5b5b5b; font-family: 'Open Sans', sans-serif; line-height: 18px; vertical-align: top; text-align: right;">
                                                        <small>INV</small>
                                                        <b>#{{ $invoice->no_invoice }}</b><br />
                                                        <small>Jth Tempo {{ $due_date_format }}</small><br>
                                                        @if ($invoice->status === 'unpaid')
                                                            <small>Status <span style="color:red">UNPAID</span></small>
                                                        @else
                                                            <small>Status <span style="color:green">PAID</span></small>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<!-- /Header -->
<!-- Order Details -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable">
    <tbody>
        <tr>
            <td>
                <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable">
                    <tbody>
                        <tr>
                        <tr class="hiddenMobile">
                            <td height="60"></td>
                        </tr>
                        <tr class="visibleMobile">
                            <td height="40"></td>
                        </tr>
                        <tr>
                            <td>
                                <table width="600" border="0" cellpadding="0" cellspacing="0" align="center"
                                    class="fullPadding">
                                    <tbody>
                                        <tr>
                                            <th style="font-size: 16px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; font-weight: normal; line-height: 1; vertical-align: top; padding: 0 0 5px 0;"
                                                width="65%" align="left">
                                                Item
                                            </th>
                                            <th style="font-size: 16px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; font-weight: normal; line-height: 1; vertical-align: top; padding: 0 0 5px;"
                                                align="left">
                                                Periode
                                            </th>
                                            <th style="font-size: 16px; font-family: 'Open Sans', sans-serif; color: #1e2b33; font-weight: normal; line-height: 1; vertical-align: top; padding: 0 0 5px;"
                                                align="right">
                                                Subtotal
                                            </th>
                                        </tr>
                                        <tr>
                                            <td height="1" style="background: #bebebe;" colspan="4"></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 16px; font-family: 'Open Sans', sans-serif; color: #646a6e;  line-height: 1;  vertical-align: top; padding:10px 0;"
                                                class="article">
                                                {{ $invoice->item }}
                                            </td>
                                            <td
                                                style="font-size: 16px; font-family: 'Open Sans', sans-serif; color: #646a6e;  line-height: 1;  vertical-align: top; padding:10px 0;">
                                                {{ $periode_format }}</td>
                                            <td style="font-size: 16px; font-family: 'Open Sans', sans-serif; color: #1e2b33;  line-height: 1;  vertical-align: top; padding:10px 0;"
                                                align="right">{{ $amount_format }}</td>
                                        </tr>

                                        <tr>
                                            <td height="1" colspan="4" style="border-bottom:1px solid #e4e4e4">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td height="20"></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<!-- /Order Details -->
<!-- Total -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable">
    <tbody>
        <tr>
            <td>
                <table width="600" border="0" cellpadding="0" cellspacing="0" align="center"
                    class="fullTable">
                    <tbody>
                        <tr>
                            <td>

                                <!-- Table Total -->
                                <table width="600" border="0" cellpadding="0" cellspacing="0" align="center"
                                    class="fullPadding">
                                    <tbody>
                                        <tr>
                                            <td
                                                style="font-size: 16px; font-family: 'Open Sans', sans-serif; color: #646a6e; line-height: 22px; vertical-align: top; text-align:right; ">
                                                Subtotal
                                            </td>
                                            <td style="font-size: 16px; font-family: 'Open Sans', sans-serif; color: #646a6e; line-height: 22px; vertical-align: top; text-align:right; white-space:nowrap;"
                                                width="80">
                                                {{ $amount_format }}
                                            </td>
                                        </tr>
                                        @if ($invoice->ppn !== null)
                                            <tr>
                                                <td
                                                    style="font-size: 16px; font-family: 'Open Sans', sans-serif; color: #646a6e; line-height: 22px; vertical-align: top; text-align:right; ">
                                                    PPN {{ $invoice->ppn }}%
                                                </td>
                                                <td
                                                    style="font-size: 16px; font-family: 'Open Sans', sans-serif; color: #646a6e; line-height: 22px; vertical-align: top; text-align:right; ">
                                                    {{ $ppn_format }}
                                                </td>
                                            </tr>
                                        @endif
                                        @if ($invoice->discount !== null)
                                            <td
                                                style="font-size: 16px; font-family: 'Open Sans', sans-serif; color: #646a6e; line-height: 22px; vertical-align: top; text-align:right; ">
                                                Disc {{ $invoice->discount }}%
                                            </td>
                                            <td
                                                style="font-size: 16px; font-family: 'Open Sans', sans-serif; color: #646a6e; line-height: 22px; vertical-align: top; text-align:right; ">
                                                {{ $discount_format }}
                                            </td>
                                        @endif
                        </tr>
                        <tr>
                            <td
                                style="font-size: 16px; font-family: 'Open Sans', sans-serif; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                                <strong>Total</strong>
                            </td>
                            <td
                                style="font-size: 16px; font-family: 'Open Sans', sans-serif; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                                <strong>{{ $total_format }}</strong>
                            </td>
                        </tr>

                    </tbody>
                </table>
                <!-- /Table Total -->

            </td>
        </tr>
    </tbody>
</table>

<!-- /Total -->
<!-- Information -->
<table width="100%" border="0" style="margin-top:30px" cellpadding="0" cellspacing="0" align="center"
    class="fullTable">
    <tbody>
        <tr>
            <td>
                <table width="600" border="0" cellpadding="0" cellspacing="0" align="center"
                    class="fullTable" bgcolor="#ffffff">
                    <tbody>
                        <tr>
                        <tr class="hiddenMobile">
                            <td height="60"></td>
                        </tr>
                        <tr class="visibleMobile">
                            <td height="40"></td>
                        </tr>
                        <tr>
                            <td>
                                <table width="600" border="0" cellpadding="0" cellspacing="0" align="center"
                                    class="fullPadding">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <table width="220" border="0" cellpadding="0" cellspacing="0"
                                                    align="left" class="col">

                                                    <tbody>
                                                        <tr>
                                                            <td
                                                                style="font-size: 16px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; line-height: 1; vertical-align: top; ">
                                                                <strong>BILLING INFORMATION</strong>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="100%" height="10"></td>
                                                        </tr>
                                                        <tr>
                                                            <td
                                                                style="font-size: 16px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; line-height: 20px; vertical-align: top; ">
                                                                {{ $company->name }}<br>
                                                                {{ $company->address }}<br>
                                                                {{ $company->wa }}<br>
                                                                {{ $company->email }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>


                                                <table width="220" border="0" cellpadding="0" cellspacing="0"
                                                    align="right" class="col">
                                                    <tbody>
                                                        <tr class="visibleMobile">
                                                            <td height="20"></td>
                                                        </tr>
                                                        <tr>
                                                            <td
                                                                style="font-size: 16px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; line-height: 1; vertical-align: top; ">
                                                                <strong>PAYMENT METHOD</strong>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="100%" height="10"></td>
                                                        </tr>
                                                        <tr>
                                                            <td
                                                                style="font-size: 16px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; line-height: 20px; vertical-align: top; ">
                                                                {{ $company->bank }}<br>
                                                                a.n {{ $company->holder }}
                                                                <br><br>
                                                                <a href="{{ $invoice->payment_url }}"
                                                                    style="color: #1f01cc; text-decoration:underline;">Klik
                                                                    untuk bayar online</a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        {{-- <tr>
                            <td>
                                <table width="480" border="0" cellpadding="0" cellspacing="0" align="center"
                                    class="fullPadding">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <table width="220" border="0" cellpadding="0" cellspacing="0"
                                                    align="left" class="col">
                                                    <tbody>
                                                        <tr class="hiddenMobile">
                                                            <td height="35"></td>
                                                        </tr>
                                                        <tr class="visibleMobile">
                                                            <td height="20"></td>
                                                        </tr>
                                                        <tr>
                                                            <td
                                                                style="font-size: 11px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; line-height: 1; vertical-align: top; ">
                                                                <strong>SHIPPING INFORMATION</strong>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="100%" height="10"></td>
                                                        </tr>
                                                        <tr>
                                                            <td
                                                                style="font-size: 18px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; line-height: 20px; vertical-align: top; ">
                                                                Sup Inc<br> Another Place, Somewhere<br> New York NY<br>
                                                                4468, United States<br> T: 202-555-0171
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>


                                                <table width="220" border="0" cellpadding="0" cellspacing="0"
                                                    align="right" class="col">
                                                    <tbody>
                                                        <tr class="hiddenMobile">
                                                            <td height="35"></td>
                                                        </tr>
                                                        <tr class="visibleMobile">
                                                            <td height="20"></td>
                                                        </tr>
                                                        <tr>
                                                            <td
                                                                style="font-size: 11px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; line-height: 1; vertical-align: top; ">
                                                                <strong>SHIPPING METHOD</strong>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="100%" height="10"></td>
                                                        </tr>
                                                        <tr>
                                                            <td
                                                                style="font-size: 18px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; line-height: 20px; vertical-align: top; ">
                                                                UPS: U.S. Shipping Services
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr> --}}
                        <tr class="hiddenMobile">
                            <td height="60"></td>
                        </tr>
                        <tr class="visibleMobile">
                            <td height="30"></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<!-- /Information -->
<table width="100%" style="margin-top:50px" border="0" cellpadding="0" cellspacing="0" align="center"
    class="fullTable">

    <tr>
        <td>
            <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable"
                style="border-radius: 0 0 10px 10px;">
                <tr>
                    <td>
                        <table width="400" border="0" cellpadding="0" cellspacing="0" align="center"
                            class="fullPadding">
                            <tbody>
                                <tr>
                                    <td
                                        style="font-size: 18px; color: #5b5b5b; font-family: 'Open Sans', sans-serif; line-height: 18px; vertical-align: top; text-align: center;">
                                        {{ $company->note }}<br>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr class="spacer">
                    <td height="50"></td>
                </tr>

            </table>
        </td>
    </tr>
    <tr>
        <td height="20"></td>
    </tr>
</table>
