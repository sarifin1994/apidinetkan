<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    
</head>
<body style="font-family: 'Inter', sans-serif;background-color: #f8f9fc;margin: 0;padding: 20px;color: #333;">
    <div class="container" style="width: 100%;max-width: 600px;margin: auto;background: white;padding: 30px;border-radius: 12px;box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
    <img src="{{ $logoPath }}">
        <div class="header" style="display: flex;align-items: center;justify-content: space-between;border-bottom: 2px solid #f1f3f7;padding-bottom: 10px;">
            <h2 style="margin: 0;font-size: 20px;font-weight: 600;">Invoice #{{ $invoice->no_invoice }}</h2>
            
            @if ($invoice->status->value === 1)
              <span class="badge" style="background: #eceff4;padding: 6px 12px;border-radius: 12px;font-size: 14px;font-weight: bold;">Unpaid</span>
            @else
              <span class="badge" style="background: #eceff4;padding: 6px 12px;border-radius: 12px;font-size: 14px;font-weight: bold;">Paid</span>
            @endif
        </div>
        <p>Issued date: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d.m.Y') }}</p>
        <div class="info-section" style="display: flex;justify-content: space-between;margin-top: 5px;padding: 15px;border-radius: 8px;">
            <table style="width: 100%;">
              <tr>
                <td>
                    <div class="info-box">
                      <h3>From:</h3>
                      <strong>{{ $settings->name }}</strong><br>
                      {{ $settings->address }}
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                    <div><hr></div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="info-box">
                      <h3>To:</h3>
                      <strong>{{ $invoice->admin->name }}</strong><br>
                      <p style="white-space: normal; word-break: break-word;">{{ $invoice->admin->address }}</p>
                  </div>
                </td>
              </tr>
            </table>
        </div>
        <div class="table-container" style="margin-top: 20px;background: #fff;padding: 15px;border-radius: 8px;box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);">
            <h3>Items</h3>
            <table class="table" style="width: 100%;border-collapse: collapse;">
                <tr>
                    <th style="padding: 12px;text-align: left;border-bottom: 1px solid #e5e7eb;background: #f9fafb;font-weight: bold;">ITEM</th>
                    <th style="padding: 12px;text-align: left;border-bottom: 1px solid #e5e7eb;background: #f9fafb;font-weight: bold;">PRICE</th>
                </tr>
                <tr>
                    <td style="padding: 12px;text-align: left;border-bottom: 1px solid #e5e7eb;">Service : {{ $invoice->item }}</td>
                    <td style="padding: 12px;text-align: left;border-bottom: 1px solid #e5e7eb;">Rp{{ number_format($invoice->price, 0, '.', '.') }}</td>
                </tr>
                @if ($invoice->ppn > 0)
                <tr>
                    <td style="padding: 12px;text-align: left;border-bottom: 1px solid #e5e7eb;">PPN ({{ $invoice->ppn }}%)</td>
                    <td style="padding: 12px;text-align: left;border-bottom: 1px solid #e5e7eb;">Rp{{ number_format(($invoice->price * $invoice->ppn / 100), 0, '.', '.') }}</td>
                </tr>
                @endif
                
                
                
                <!-- untuk masukin rincian adons -->
                @if($adons)
                <?php $total_ad = 0; ?>
                      @foreach($adons as $ad)
                      <?php $total_ad = $total_ad + $ad->price;?>
                      @if($ad->ppn > 0)
                        <?php 
                        $total_ppn_ad = $ad->ppn * $total_ad / 100;
                        ?>
                      @endif
                <tr>
                    <td style="padding: 12px;text-align: left;border-bottom: 1px solid #e5e7eb;">{{ $ad->description }}</td>
                    <td style="padding: 12px;text-align: left;border-bottom: 1px solid #e5e7eb;">Rp{{ number_format((($ad->qty * $ad->price) + $total_ppn_ad), 0, '.', '.') }}</td>
                </tr>
                  @endforeach
                @endif

                @if ($invoice->discount > 0)
                <tr>
                    <td style="padding: 12px;text-align: left;border-bottom: 1px solid #e5e7eb;">Discount ({{ $invoice->discount }}%)</td>
                    <td style="padding: 12px;text-align: left;border-bottom: 1px solid #e5e7eb;">-Rp{{ number_format(($invoice->price * $invoice->dicount / 100), 0, '.', '.') }}</td>
                </tr>
                @endif
                @if ($invoice->discountCoupon > 0)
                <tr>
                    <td style="padding: 12px;text-align: left;border-bottom: 1px solid #e5e7eb;">Discount Coupon</td>
                    <td style="padding: 12px;text-align: left;border-bottom: 1px solid #e5e7eb;">-Rp{{ number_format($invoice->discount_coupon, 0, '.', '.') }}</td>
                </tr>
                @endif
                <tr>
                    <td style="font-weight: bold;padding: 12px;text-align: left;border-bottom: 1px solid #e5e7eb;">Total incl. VAT:</td>
                    <td style="font-weight: bold;padding: 12px;text-align: left;border-bottom: 1px solid #e5e7eb;">Rp{{ number_format(($invoice->price + $invoice->total_ppn + $invoice->price_adon  + $invoice->price_adon_monthly), 0, '.', '.') }}</td>
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
