<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: #007bff;
            color: #fff;
            padding: 15px;
            text-align: center;
            font-size: 20px;
            border-radius: 8px 8px 0 0;
        }
        .content {
            padding: 20px;
            text-align: left;
        }
        .footer {
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #777;
        }
        .button {
            display: inline-block;
            background: #28a745;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            Invoice Notification
        </div>
        <div class="content">
            <p>Kepada {{$data['fullname']}},</p>
            <p>Kami berharap email ini sampai kepada Anda dengan baik. Silakan rincian faktur Anda:</p>
            <p><strong>Nomor Invoice :</strong> {{$data['no_invoice']}}</p>
            <p><strong>Service : </strong>{{$data['item']}}</p>
            <p><strong>Tanggal Invoice : </strong> {{$data['invoice_date']}}</p>
            <p><strong>Tanggal Jatuh Tempo: </strong> {{$data['due_date']}}</p>
            <p><strong>Total Pembayaran : </strong> {{$data['total']}}</p>
            @if(isset($data['status']))
              @if($data['status'] == 2)
              <p>Invoice anda sudah terbayarkan. Anda dapat melihat faktur Anda dengan mengklik tombol di bawah ini:</p>
              @else
              <p>Harap melakukan pembayaran sebelum tanggal {{$data['max_date']}} untuk menghindari biaya keterlambatan. Anda dapat melihat dan membayar faktur Anda dengan mengklik tombol di bawah ini:</p>
              @endif
            @else
            <p>Harap melakukan pembayaran sebelum tanggal {{$data['max_date']}} untuk menghindari biaya keterlambatan. Anda dapat melihat dan membayar faktur Anda dengan mengklik tombol di bawah ini:</p>
            @endif
            <p><a href="{{$data['url']}}" class="button">Lihat Invoice</a></p>
            <p>Terima Kasih</p>
            <p>Salam,</p>
            <p>Dinetkan</p>
        </div>
        <div class="footer">
        <div>PT Putra Garsel Interkoneksi</div>
        <div style="margin-top:10px"></div>
        <div>Jalan Asia-Afrika No.114-119 Wisma Bumi Putera Lt.3 Suite .301 B
        Kb. Pisang, Kec. Sumur Bandung, Kota Bandung, Jawa Barat 40112</div>
        <div style="margin-top:10px"></div>
        <div>+62 822-473-377</div>
        <div style="margin-top:10px"></div>
        <div>&copy; <script>document.write( new Date().getFullYear() );</script> All Rights Reserved PT Putra Garsel Interkoneksi.</div>
        
        <div style="margin-top:10px"></div>
        <div></div>
		<center>
			<table style="border-collapse:collapse;border-spacing:0;border-width:0">
			  <tbody>
				<tr>
				  <td style="padding:0 5px 0 0;background-color:#f9f9f9" width="24">
					<a title="Ikuti kami di Facebook" href="https://www.facebook.com/dinetkan" target="_blank" data-saferedirecturl="https://www.google.com/url?q=http://newsletter.rw.srs-x.net/lt.php?tid%3Df0kLWl1eUFFZVhVWC1UBSFZWVg9ICFJWAB4PUAEFUV4PUVYMUl0ZBA8HXFdYCF1IVwcFCkgFB11VHlQAV1UZXVYBUQAGAVVZWV9bSAtSBgBWBwcOSARTXAceAlEAABlaDwxSFAFaBFUIXw4NDQlRVA&amp;source=gmail&amp;ust=1740465317660000&amp;usg=AOvVaw2GSz7dHUry6wCB_4GM21oG">
					  <img src="https://ci3.googleusercontent.com/meips/ADKq_Na9r-SV8QdjnBYB2FDbdJPt0vwyA8RvukGLvhnWyY30jOGZGlQCjuK6B0Qjvtb2wBNKrH1QpcNM0iOAfNJr-ZsZ1wZeqw=s0-d-e1-ft#https://cdn01.rumahweb.com/nws/assets/sm-fb.png" alt="Facebook" width="24" height="24" align="left" border="0" hspace="0" vspace="0" class="CToWUd" data-bit="iit">
					</a>
				  </td>
				  <td style="padding:0 5px 0 0;background-color:#f9f9f9" width="24">
					<a title="Ikuti kami di Instagram" href="https://www.instagram.com/dinetkan/" target="_blank" data-saferedirecturl="https://www.google.com/url?q=http://newsletter.rw.srs-x.net/lt.php?tid%3Df0kFWFAJUwMPABVVWglQSFYABQlICFUHBh5XXAEABFsBBwJfVw8ZBA8HXFdYCF1IVwcFCkgFB11VHlQAV1UZXVYBUQAGAVVZWV9bSAtSBgBWBwcOSARTXAceAlEAABlaDwxSFAFaBFUIXw4NDQlRVA&amp;source=gmail&amp;ust=1740465317660000&amp;usg=AOvVaw3sstxlvE-Qk_EJ5V3_3D50">
					  <img src="https://ci3.googleusercontent.com/meips/ADKq_NZD8az40aRmPZxSnnEBueE6qJQFHNiFheHxYto8-UvNlW5w0OByBuS-_iDECsrzPM8tXNfck0lAhV0a6w5WU5ecNZVxpA=s0-d-e1-ft#https://cdn01.rumahweb.com/nws/assets/sm-ig.png" alt="Instagram" width="24" height="24" align="left" border="0" hspace="0" vspace="0" class="CToWUd" data-bit="iit">
					</a>
				  </td>
				</tr>
			  </tbody>
			</table>
		</center>
        </div>
    </div>
</body>
</html>
