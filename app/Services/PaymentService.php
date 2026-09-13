<?php

namespace App\Services;

use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    public static function capture(ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->payment_status !== 'hold') {
            throw new \Exception('Payment is not authorized yet.');
        }

        // Mocked API call to Moyasar to Capture the frozen amount
        // $apiKey = env('MOYASAR_SECRET_KEY');
        // Http::withBasicAuth($apiKey, '')->post("https://api.moyasar.com/v1/payments/{$serviceRequest->payment_id}/capture");

        $serviceRequest->payment_status = 'paid';
        $serviceRequest->status = 'completed';
        $serviceRequest->save();

        return $serviceRequest;
    }
}
