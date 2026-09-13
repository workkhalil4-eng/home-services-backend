<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProviderProfile;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    public function updateProfile(Request $request)
    {
        $request->validate([
            'max_travel_distance' => 'nullable|integer',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        $profile = ProviderProfile::firstOrCreate(['user_id' => $request->user()->id]);

        if ($request->has('max_travel_distance')) {
            $profile->max_travel_distance = $request->max_travel_distance;
        }

        $profile->save();

        if ($request->has('categories')) {
            $profile->categories()->sync($request->categories);
        }

        return response()->json(['message' => 'Profile updated successfully', 'data' => $profile]);
    }

    public function uploadKyc(Request $request)
    {
        $request->validate([
            'id_card_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'certificate_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $profile = ProviderProfile::firstOrCreate(['user_id' => $request->user()->id]);

        if ($request->hasFile('id_card_image')) {
            $path = $request->file('id_card_image')->store('kyc', 'local');
            $profile->id_card_image = $path;
        }

        if ($request->hasFile('certificate_image')) {
            $path = $request->file('certificate_image')->store('kyc', 'local');
            $profile->certificate_image = $path;
        }

        $profile->kyc_status = 'pending'; // Reset status if they re-upload
        $profile->save();

        return response()->json(['message' => 'KYC documents uploaded successfully. Pending admin approval.']);
    }

    public function toggleAvailability(Request $request)
    {
        $profile = ProviderProfile::where('user_id', $request->user()->id)->firstOrFail();

        if ($profile->kyc_status !== 'approved') {
            return response()->json(['message' => 'Cannot toggle availability. KYC is not approved.'], 403);
        }

        $profile->is_available = !$profile->is_available;
        $profile->save();

        return response()->json(['message' => 'Availability updated', 'is_available' => $profile->is_available]);
    }

    public function acceptRequest(Request $request, $id)
    {
        try {
            // Use a database transaction to prevent race conditions
            $serviceRequest = \Illuminate\Support\Facades\DB::transaction(function () use ($id, $request) {
                // lockForUpdate prevents other transactions from modifying this row until this transaction is done
                $req = \App\Models\ServiceRequest::lockForUpdate()->findOrFail($id);

                if ($req->status !== 'pending') {
                    throw new \Exception('تم إسناد الطلب لفني آخر أو تم إلغاؤه.');
                }

                $req->provider_id = $request->user()->id;
                $req->status = 'accepted'; // Now wait for customer payment
                $req->save();

                return $req;
            });

            broadcast(new \App\Events\ServiceRequestAcceptedEvent($serviceRequest))->toOthers();

            return response()->json(['message' => 'تم قبول الطلب بنجاح. بانتظار دفع العميل.', 'data' => $serviceRequest]);
            
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409); // Conflict
        }
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $profile = ProviderProfile::where('user_id', $request->user()->id)->firstOrFail();
        $profile->latitude = $request->latitude;
        $profile->longitude = $request->longitude;
        $profile->save();

        return response()->json(['message' => 'Location updated']);
    }

    public function completeRequest(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);

        if ($serviceRequest->provider_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($serviceRequest->status !== 'in_progress') {
            return response()->json(['message' => 'Service is not in progress'], 400);
        }

        try {
            \App\Services\PaymentService::capture($serviceRequest);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        broadcast(new \App\Events\ServiceRequestCompletedEvent($serviceRequest))->toOthers();

        return response()->json(['message' => 'تم إنجاز الخدمة بنجاح.', 'data' => $serviceRequest]);
    }
}
