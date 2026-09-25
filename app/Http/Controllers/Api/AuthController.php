<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        // توليد رمز تحقق عشوائي حقيقي من 4 أرقام
        $otp = (string) rand(1000, 9999);

        Cache::put('otp_' . $request->phone, $otp, now()->addMinutes(5));

        return response()->json([
            'message' => 'تم إرسال رمز التحقق',
            'simulated_otp' => $otp // إرجاع الرمز لمحاكاة وصول رسالة SMS في التطبيق
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string',
        ]);

        $cachedOtp = Cache::get('otp_' . $request->phone);

        // التحقق من أن الرمز مطابق للرمز العشوائي (لا يوجد 1234 بعد الآن)
        if ($cachedOtp !== $request->otp) {
            return response()->json(['message' => 'رمز التحقق غير صحيح أو منتهي الصلاحية'], 400);
        }

        $user = User::firstOrCreate(
            ['phone' => $request->phone],
            ['role' => $request->role ?? 'customer']
        );

        Cache::forget('otp_' . $request->phone);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'user' => $user,
            'token' => $token
        ]);
    }
}