<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\MpesaTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    private function accessToken()
    {
        $response = Http::withBasicAuth(
            config('services.mpesa.key'),
            config('services.mpesa.secret')
        )->get(
            'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
        );

        if (!$response->successful()) {

            Log::error('M-Pesa Access Token Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            throw new \Exception('Failed to get M-Pesa access token');
        }

        return $response->json()['access_token'];
    }

    public function stkPush(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1']
        ]);

        $user = auth()->user();

        if (!$user) {
            return back()->with('error', 'User not authenticated');
        }

        $phone = $user->phone_no;

        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        }

        $amount = (int) $request->amount;

        $timestamp = now()->format('YmdHis');

        $password = base64_encode(
            config('services.mpesa.shortcode')
            . config('services.mpesa.passkey')
            . $timestamp
        );

        $response = Http::withToken(
            $this->accessToken()
        )->post(
            'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest',
            [
                "BusinessShortCode" => config('services.mpesa.shortcode'),
                "Password" => $password,
                "Timestamp" => $timestamp,
                "TransactionType" => "CustomerPayBillOnline",
                "Amount" => $amount,
                "PartyA" => $phone,
                "PartyB" => config('services.mpesa.shortcode'),
                "PhoneNumber" => $phone,
                "CallBackURL" => "https://untangled-uncolored-reseller.ngrok-free.dev/payments/callback",
                "AccountReference" => "Contribution",
                "TransactionDesc" => "Group Contribution"
            ]
        );

        Log::info('STK Push Response', [
            'response' => $response->json()
        ]);

        if (
            !$response->successful() ||
            !isset($response['ResponseCode'])
        ) {

            return back()->with(
                'error',
                'Failed to send STK Push request'
            );
        }

        MpesaTransaction::create([
            'user_id' => $user->id,
            'group_id' => session('active_group_id'),
            'amount' => $amount,
            'phone' => $phone,
            'checkout_request_id' =>
                $response['CheckoutRequestID'] ?? null,
            'merchant_request_id' =>
                $response['MerchantRequestID'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with(
            'success',
            'STK Push sent. Check your phone and enter your M-Pesa PIN.'
        );
    }

    public function callback(Request $request)
{
    $data = $request->all();

    Log::info('M-Pesa Callback', $data);

    $checkoutRequestId =
        $data['Body']['stkCallback']['CheckoutRequestID'];

    $resultCode =
        $data['Body']['stkCallback']['ResultCode'];

    $transaction = MpesaTransaction::where(
        'checkout_request_id',
        $checkoutRequestId
    )->first();

    if (!$transaction) {

        Log::error('Transaction not found', [
            'checkout_request_id' => $checkoutRequestId
        ]);

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted'
        ]);
    }

    if ($resultCode == 0) {

    $items =
        $data['Body']['stkCallback']['CallbackMetadata']['Item'];

    $receiptNumber = collect($items)
        ->firstWhere('Name', 'MpesaReceiptNumber')['Value'] ?? null;

    $transaction->update([
        'status' => 'paid',
        'receipt_number' => $receiptNumber,
    ]);

    Contribution::create([
    'mpesa_transaction_id' => $transaction->id,
    'group_id' => $transaction->group_id,
    'user_id' => $transaction->user_id,
    'amount' => $transaction->amount,
    'month' => now()->month,
    'year' => now()->year,
    'status' => 'paid',
    'paid_at' => now(),
]);

    Log::info('Payment marked as paid', [
        'transaction_id' => $transaction->id,
        'receipt_number' => $receiptNumber,
    ]);
} else {

        $transaction->update([
            'status' => 'failed'
        ]);

        Log::warning('Payment failed', [
            'result_code' => $resultCode
        ]);
    }

    return response()->json([
        'ResultCode' => 0,
        'ResultDesc' => 'Accepted'
    ]);
}
}