<?php

namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Payments\Services\TripayService;
use Modules\Payments\Services\MidtransService;
use Modules\Payments\Services\MemberPaymentService;
use Modules\Payments\Repositories\Contracts\InvoiceRepositoryInterface;
use Modules\Payments\Services\DuitkuService;
use Modules\Payments\Services\XenditService;
use Modules\Payments\ValueObjects\DuitkuConfig;
use Modules\Payments\ValueObjects\MidtransConfig;
use Modules\Payments\ValueObjects\PriceData;
use Modules\Payments\ValueObjects\TripayConfig;
use Modules\Payments\ValueObjects\XenditConfig;

final class MemberInvoiceController extends Controller
{
    public function __construct(
        protected InvoiceRepositoryInterface $invoiceRepo,
        protected MidtransService $midtransService,
        protected TripayService $tripayService,
        protected DuitkuService $duitkuService,
        protected XenditService $xenditService,
        protected MemberPaymentService $memberPaymentService,
    ) {}

    public function pay(string $no_invoice): \Illuminate\Contracts\View\View
    {
        $invoice = $this->invoiceRepo->findByNoInvoice($no_invoice);
        abort_unless($invoice, 404);

        $ppp      = $invoice->pppoe ?? null;
        $company  = $invoice->company ?? null;
        $settings = $invoice->billingSetting ?? null;

        $activeGateway   = $settings->payment_gateway ?? null;
        $config = match ($activeGateway) {
            'midtrans' => $invoice->group->midtrans,
            'tripay' => $invoice->group->tripay,
            'duitku' => $invoice->group->duitku,
            'xendit' => $invoice->group->xendit,
            default => null,
        };
        $periode_format  = indonesiaDateFormat($invoice->period);

        $tripayMethods = [];

        $priceData = new PriceData(
            $invoice->price,
            $invoice->ppn,
            $invoice->discount,
            $config->admin_fee ?? 0,
        );

        if ($activeGateway === 'tripay' && $config) {
            $this->tripayService->setConfig(new TripayConfig(
                merchantCode: $config->merchant_code,
                apiKey: $config->api_key,
                privateKey: $config->private_key,
                adminFee: $config->admin_fee,
            ));
            $tripayMethods = $this->tripayService->getPaymentMethods($priceData);
        }

        return view('billing.invoice.bayar', compact(
            'invoice',
            'company',
            'ppp',
            'periode_format',
            'activeGateway',
            'tripayMethods',
            'config',
            'settings',
            'priceData',
        ));
    }

    public function processPayment(Request $request, string $no_invoice)
    {
        $request->validate(['payment_method' => 'nullable|string']);

        $invoice = $this->invoiceRepo->findByNoInvoice($no_invoice);
        abort_unless($invoice, 404);

        $settings = $invoice->billingSetting;
        $activeGateway   = $settings->payment_gateway ?? null;
        $config = match ($activeGateway) {
            'midtrans' => $invoice->group->midtrans,
            'duitku' => $invoice->group->duitku,
            'tripay' => $invoice->group->tripay,
            'xendit' => $invoice->group->xendit,
            default => null,
        };

        if (!$config) {
            return back()->with('error', 'Invalid payment gateway');
        }

        $priceData = new PriceData(
            $invoice->price,
            $invoice->ppn,
            $invoice->discount,
            $config->admin_fee,
        );

        if ($activeGateway === 'midtrans') {
            $this->midtransService->setConfig(new MidtransConfig(
                idMerchant: $config->id_merchant,
                serverKey: $config->server_key,
                clientKey: $config->client_key,
                isProduction: $config->status == 1,
                adminFee: $config->admin_fee,
            ));

            $snap_token = $this->midtransService->generateSnapToken($invoice);

            return response()->json(['token' => $snap_token]);
        } else if ($activeGateway === 'duitku') {
            $this->duitkuService->setConfig(new DuitkuConfig(
                idMerchant: $config->id_merchant,
                apiKey: $config->api_key,
                isProduction: $config->status == 1,
                adminFee: $config->admin_fee,
            ));

            $response = $this->duitkuService->createTransaction($invoice);

            return response()->json(['token' => $response['reference']]);
        } else if ($activeGateway === 'tripay') {
            $this->tripayService->setConfig(new TripayConfig(
                merchantCode: $config->merchant_code,
                apiKey: $config->api_key,
                privateKey: $config->private_key,
                adminFee: $config->admin_fee,
            ));
            $selectedMethod = $request->input('payment_method');

            $validMethods = array_map(fn($method) => $method->code, $this->tripayService->getPaymentMethods($priceData));
            if (!in_array($selectedMethod, $validMethods, true)) {
                return back()->with('error', 'Invalid payment method');
            }

            $checkoutUrl = $this->tripayService->createTransaction($invoice, $selectedMethod);

            if ($checkoutUrl) {
                return response()->json(['url' => $checkoutUrl]);
            }
        } else if ($activeGateway === 'xendit') {
            $this->xenditService->setConfig(new XenditConfig(
                publicKey: $config->public_key,
                secretKey: $config->secret_key,
                webhookVerificationKey: $config->webhook_verification_key,
                isProduction: $config->status == 1,
                adminFee: $config->admin_fee,
            ));
            $response = $this->xenditService->createTransaction($invoice);

            if ($response) {
                return response()->json(['url' => $response->getInvoiceUrl()]);
            }
        } else {
            return back()->with('error', 'Invalid payment gateway');
        }

        return back()->with('error', 'Failed to create Tripay transaction');
    }

    public function midtransNotification(Request $request)
    {
        $order_id = $request->get('order_id');
        $invoice = $this->invoiceRepo->findByNoInvoice($order_id);
        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }

        $midtransConfig = $invoice->group->midtrans;

        if (!$midtransConfig || !$midtransConfig->server_key) {
            return response()->json(['error' => 'Invalid configuration'], 400);
        }

        $this->midtransService->setConfig($midtransConfig);
        $notif = $this->midtransService->getNotification($request->all());

        $handledInvoice = $this->midtransService->handleNotification($notif);
        if ($handledInvoice) {
            $this->memberPaymentService->handleSuccessfulPayment($handledInvoice);
        }

        return response()->json(['success' => true]);
    }

    public function duitkuNotification(Request $request)
    {
        $config = $this->duitkuService->config;
        $merchantCode = $request->input('merchantCode');
        $amount = $request->input('amount');
        $merchantOrderId = $request->input('merchantOrderId');
        $productDetail = $request->input('productDetail');
        $additionalParam = $request->input('additionalParam');
        $paymentCode = $request->input('paymentCode');
        $resultCode = $request->input('resultCode');
        $merchantUserId = $request->input('merchantUserId');
        $reference = $request->input('reference');
        $signature = $request->input('signature');
        $publisherOrderId = $request->input('publisherOrderId');
        $spUserHash = $request->input('spUserHash');
        $settlementDate = $request->input('settlementDate');
        $issuerCode = $request->input('issuerCode');

        if (empty($merchantCode) || empty($amount) || empty($merchantOrderId) || empty($signature)) {
            return response()->json(['error' => 'Bad Parameter'], 400);
        }

        $params = $merchantCode . $amount . $merchantOrderId . $config->getApiKey();
        $calcSignature = md5($params);

        if ($signature !== $calcSignature) {
            return response()->json(['error' => 'Bad Signature'], 400);
        }

        $invoice = $this->invoiceRepo->findByNoInvoice($merchantOrderId);
        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }

        $this->memberPaymentService->handleSuccessfulPayment($invoice);
        return response()->json(['success' => true, 'message' => 'Invoice successfully paid and processed']);
    }

    public function tripayNotification(Request $request)
    {
        $order_id = $request->get('merchant_ref');
        $invoice = $this->invoiceRepo->findByNoInvoice($order_id);
        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }

        $tripayConfig = $invoice->group->tripay;
        if (!$tripayConfig) {
            return response()->json(['error' => 'Tripay configuration not found'], 404);
        }

        $this->tripayService->setConfig($tripayConfig);
        if (!$this->tripayService->validateSignature($request->all(), $request->header('X-Callback-Signature'))) {
            abort(403, 'Invalid signature');
        }

        $handledInvoice = $this->tripayService->handleNotification($request->all());
        if ($handledInvoice) {
            $this->memberPaymentService->handleSuccessfulPayment($handledInvoice);
            return response()->json(['success' => true, 'message' => 'Invoice successfully paid and processed']);
        }

        return response()->json(['message' => 'Transaction status not handled'], 400);
    }

    public function xenditNotification(Request $request)
    {
        try {
            $invoice = $this->xenditService->handleNotification($request);

            if (!$invoice) {
                return response()->json(['error' => 'Transaction not handled'], 400);
            }

            $this->memberPaymentService->handleSuccessfulPayment($invoice);

            return response()->json(['success' => true, 'message' => 'Invoice successfully paid and processed']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Transaction not handled'], 400);
        }
    }
}
