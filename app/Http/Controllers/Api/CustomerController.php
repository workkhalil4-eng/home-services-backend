<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function getCategories()
    {
        return response()->json(Category::where('is_active', true)->get());
    }

    public function getServices($categoryId)
    {
        return response()->json(Service::where('category_id', $categoryId)->where('is_active', true)->get());
    }

    public function getOrders(Request $request)
    {
        $orders = ServiceRequest::with(['service', 'provider'])
            ->where('customer_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Map to return simplified data structure for the app
        $mapped = $orders->map(function($order) {
            return [
                'id' => $order->id,
                'title' => $order->service->name ?? 'خدمة عامة',
                'provider' => $order->provider ? ('الفني: ' . $order->provider->name) : 'بانتظار موافقة فني',
                'date' => $order->created_at->format('Y-m-d H:i'),
                'price' => $order->total_price ? $order->total_price . ' ر.س' : 'غير محدد',
                'status_code' => $order->status,
                'status' => $this->mapStatus($order->status),
            ];
        });

        return response()->json($mapped);
    }

    private function mapStatus($status) {
        switch($status) {
            case 'pending': return 'قيد الانتظار';
            case 'in_progress': return 'قيد المتابعة';
            case 'completed': return 'مكتمل بنجاح';
            case 'cancelled': return 'ملغي';
            default: return 'غير معروف';
        }
    }

    public function createRequest(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'description' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'required|string',
            'scheduled_at' => 'nullable|date',
        ]);

        $service = Service::findOrFail($request->service_id);

        $lat = $request->latitude;
        $lng = $request->longitude;

        $providers = \App\Models\ProviderProfile::where('is_available', true)
            ->where('kyc_status', 'approved')
            ->whereHas('categories', function($q) use ($service) {
                $q->where('categories.id', $service->category_id);
            })
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $matchingProviders = [];

        foreach ($providers as $provider) {
            $earthRadius = 6371;
            $dLat = deg2rad($provider->latitude - $lat);
            $dLng = deg2rad($provider->longitude - $lng);
            $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat)) * cos(deg2rad($provider->latitude)) * sin($dLng/2) * sin($dLng/2);
            $c = 2 * atan2(sqrt($a), sqrt(1-$a));
            $distance = $earthRadius * $c;

            if ($distance <= $provider->max_travel_distance) {
                $matchingProviders[] = $provider;
            }
        }

        if (empty($matchingProviders)) {
            return response()->json(['message' => 'عذراً، لا يوجد فنيون متاحون في منطقتك حالياً'], 404);
        }

        $serviceRequest = ServiceRequest::create([
            'customer_id' => $request->user()->id,
            'service_id' => $service->id,
            'description' => $request->description,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
            'scheduled_at' => $request->scheduled_at,
            'status' => 'pending',
            'total_price' => $service->base_price,
        ]);

        foreach ($matchingProviders as $provider) {
            broadcast(new \App\Events\NewServiceRequestEvent($serviceRequest, $provider->user_id))->toOthers();
        }

        return response()->json([
            'message' => 'Service request created successfully',
            'data' => $serviceRequest
        ], 201);
    }

    public function cancelRequest(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::where('customer_id', $request->user()->id)->findOrFail($id);

        if ($serviceRequest->status === 'completed') {
            return response()->json(['message' => 'Cannot cancel a completed request'], 400);
        }

        // If payment was authorized, void it
        if ($serviceRequest->payment_status === 'authorized') {
            // Call Moyasar void API here in reality
            $serviceRequest->payment_status = 'refunded';
        }

        $serviceRequest->status = 'canceled';
        $serviceRequest->save();

        return response()->json(['message' => 'Request canceled successfully. Any frozen amount has been released.', 'data' => $serviceRequest]);
    }

    public function trackRequest(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::with('provider.providerProfile')->findOrFail($id);

        if ($serviceRequest->customer_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Rule: Tracking is strictly visible ONLY when payment is authorized (status = in_progress)
        if ($serviceRequest->status !== 'in_progress') {
            return response()->json([
                'message' => 'Tracking is not available yet. Please authorize payment.',
                'tracking_visible' => false,
                'location' => null
            ], 403);
        }

        $profile = $serviceRequest->provider->providerProfile;

        return response()->json([
            'message' => 'Tracking active.',
            'tracking_visible' => true,
            'location' => [
                'latitude' => $profile->latitude,
                'longitude' => $profile->longitude,
            ]
        ]);
    }
}
