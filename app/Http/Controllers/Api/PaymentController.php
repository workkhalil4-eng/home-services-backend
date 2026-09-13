<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    private $apiKey;

    public function __construct()
    {
        // Using Moyasar Test Secret Key
        $this->apiKey = env('MOYASAR_SECRET_KEY', 'sk_test_dummy_key_12345');
    }

    public function authorizePayment(Request $request)
    {
        $request->validate([
            'service_request_id' => 'required|exists:service_requests,id',
            'source_id' => 'required|string', // The payment token from Flutter Moyasar SDK
        ]);

        $serviceRequest = ServiceRequest::findOrFail($request->service_request_id);

        if ($serviceRequest->payment_status === 'hold') {
            return response()->json(['message' => 'Payment already authorized'], 400);
        }

        // Mocking failure cases for Sandbox
        if ($request->source_id === 'token_fail_insufficient') {
            return response()->json(['message' => 'Payment failed: Insufficient funds'], 402);
        }
        if ($request->source_id === 'token_fail_declined') {
            return response()->json(['message' => 'Payment failed: Card declined'], 402);
        }

        // Call Moyasar API to Authorize (Sandbox mode)
        /*
        $response = Http::withBasicAuth($this->apiKey, '')
            ->post('https://api.moyasar.com/v1/payments', [
                'amount' => $serviceRequest->total_price * 100, // Halalas
                'currency' => 'SAR',
                'description' => 'Service Request #' . $serviceRequest->id,
                'source' => [
                    'type' => 'token',
                    'token' => $request->source_id
                ],
                'capture' => false,
            ]);
        */

        // For MVP & Sandbox, we will simulate success if response is 200/201 or just hardcode simulation
        // Assuming simulation for Sandbox:
        $serviceRequest->payment_status = 'hold';
        $serviceRequest->status = 'in_progress';
        $serviceRequest->payment_id = 'mock_pay_id_' . rand(1000, 9999);
        $serviceRequest->save();

        broadcast(new \App\Events\PaymentAuthorizedEvent($serviceRequest))->toOthers();

        return response()->json([
            'message' => 'Payment authorized successfully. Provider is now on the way.',
            'service_request' => $serviceRequest
        ]);
    }

    public function capturePayment(Request $request)
    {
        $request->validate([
            'service_request_id' => 'required|exists:service_requests,id',
            'payment_id' => 'required|string', // The Moyasar payment ID
        ]);

        $serviceRequest = ServiceRequest::findOrFail($request->service_request_id);

        try {
            \App\Services\PaymentService::capture($serviceRequest);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json([
            'message' => 'Payment captured successfully. Service marked as completed.',
            'service_request' => $serviceRequest
        ]);
    }

    public function voidPayment(Request $request)
    {
        $request->validate([
            'service_request_id' => 'required|exists:service_requests,id',
            'payment_id' => 'required|string', 
        ]);

        $serviceRequest = ServiceRequest::findOrFail($request->service_request_id);

        // Simulated API call to Moyasar to Void/Cancel the frozen amount
        // Http::withBasicAuth($this->apiKey, '')->post("https://api.moyasar.com/v1/payments/{$request->payment_id}/void");

        $serviceRequest->payment_status = 'refunded';
        $serviceRequest->status = 'canceled';
        $serviceRequest->save();

        return response()->json([
            'message' => 'Payment voided successfully. Amount released to customer.',
            'service_request' => $serviceRequest
        ]);
    }
}
