<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'service_request_id' => 'required|exists:service_requests,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $serviceRequest = ServiceRequest::findOrFail($request->service_request_id);

        // Ensure only the customer of this request can review
        if ($serviceRequest->customer_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Ensure service is completed
        if ($serviceRequest->status !== 'completed') {
            return response()->json(['message' => 'Cannot review an incomplete service'], 400);
        }

        // Ensure not already reviewed
        if (Review::where('service_request_id', $serviceRequest->id)->exists()) {
            return response()->json(['message' => 'Review already submitted'], 400);
        }

        $review = Review::create([
            'service_request_id' => $serviceRequest->id,
            'customer_id' => $serviceRequest->customer_id,
            'provider_id' => $serviceRequest->provider_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json(['message' => 'Review submitted successfully', 'data' => $review], 201);
    }
}
