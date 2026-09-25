<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    public function sendOtp(Request \)
    {
        \->validate([
            'phone' => 'required|string',
        ]);

        // توليد رمز تحقق عشوائي حقيقي من 4 أرقام
        \ = (string) rand(1000, 9999);

        Cache::put('otp_' . \->phone, \, now()->addMinutes(5));

        return response()->json([
            'message' => 'تم إرسال رمز التحقق',
            'simulated_otp' => \ // إرجاع الرمز لمحاكاة وصول رسالة SMS في التطبيق
        ]);
    }

    public function verifyOtp(Request \)
    {
        \->validate([
            'phone' => 'required|string',
            'otp' => 'required|string',
        ]);

        \ = Cache::get('otp_' . \->phone);

        // التحقق من أن الرمز مطابق للرمز العشوائي (لا يوجد 1234 بعد الآن)
        if (\ !== \->otp) {
            return response()->json(['message' => 'رمز التحقق غير صحيح أو منتهي الصلاحية'], 400);
        }

        \ = User::firstOrCreate(
            ['phone' => \->phone],
            ['role' => \->role ?? 'customer']
        );

        Cache::forget('otp_' . \->phone);

        \ = \->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'user' => \,
            'token' => \
        ]);
    }
}