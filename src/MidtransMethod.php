<?php

namespace Azuriom\Plugin\Midtrans;

use Azuriom\Models\User;
use Azuriom\Plugin\Shop\Cart\Cart;
use Azuriom\Plugin\Shop\Models\Payment;
use Azuriom\Plugin\Shop\Payment\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MidtransMethod extends PaymentMethod
{
    protected $id = 'midtrans';
    protected $name = 'Midtrans';
    protected $sandbox = 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    protected $production = 'https://app.midtrans.com/snap/v1/transactions';

    public function startPayment(Cart $cart, float $amount, string $currency)
    {
        $payment = $this->createPayment($cart, $amount, $currency);

        $serverKey = $this->gateway->data['server-key'];
        $clientKey = $this->gateway->data['client-key'];
        $mode = $this->gateway->data['mode'];

        $url = $mode === 'sandbox' ? $this->sandbox : $this->production;

        $headers = [
            'Authorization' => 'Basic ' . base64_encode($serverKey . ':'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        // Format invoice: MCX-(payment_id)
        $customOrderId = 'MCX-' . $payment->id;

        $body = [
            'transaction_details' => [
                'order_id' => $customOrderId,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => $payment->user->name,
                'email' => $payment->user->email,
            ],
        ];

        $response = Http::withHeaders($headers)
            ->post($url, $body);

        if (!$response->successful()) {
            return redirect()->route('shop.cart.index')->with('error', 'Gagal membuat transaksi Midtrans.');
        }

        $snapUrl = $response->json()['redirect_url'];

        return redirect()->away($snapUrl);
    }


    public function notification(Request $request, ?string $paymentId)
    {
        $serverKey = $this->gateway->data['server-key'];

        $signatureKey = hash(
            'sha512',
                $request->input('order_id') .
                $request->input('status_code') .
                $request->input('gross_amount') .
                $serverKey
        );

        if ($request->input('signature_key') !== $signatureKey) {
            return response()->json([
                'error' => 'Invalid Signature',
                'order_id' => $request->input('order_id'),
                'status_code' => $request->input('status_code'),
                'gross_amount' => $request->input('gross_amount'),
                'signature_key' => $signatureKey
        ]);
        }

        $rawId = (int) str_replace('MCX-', '', $request->input('order_id'));

        $payment = Payment::findOrFail($rawId);

        if ($request->input('transaction_status') === 'settlement' || $request->input('transaction_status') === 'capture') {
            return $this->processPayment($payment, $request->input('transaction_id'));
        } else {
            return response([
                'transaction_status: ' . $request->input('transaction_status'),
                'payment_id: ' . $rawId
            ]);
        }

        return response('OK');
    }

    public function success(Request $request)
    {
        return redirect()->route('shop.home')->with('success', trans('messages.status.success'));
    }

    public function view()
    {
        return 'midtrans::admin.midtrans';
    }

    public function rules()
    {
        return [
            'server-key' => ['required', 'string'],
            'client-key' => ['required', 'string'],
            'mode' => ['required', 'string']
        ];
    }

    public function image()
    {
        return asset('plugins/midtrans/img/midtrans.png');
    }
}
