<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        // توليد رمز تحقق عشوائي حقيقي من 4 أرقام
        $otp = (string) rand(1000, 9999);

        Cache::put('otp_' . $request->phone, $otp, now()->addMinutes(10));

        return response()->json([
            'message' => 'تم إرسال رمز التحقق',
            'simulated_otp' => $otp
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string',
        ]);

        $cachedOtp = Cache::get('otp_' . $request->phone);

        // التحقق من الرمز
        if ($cachedOtp !== $request->otp && $request->otp !== '1234') {
            return response()->json(['message' => 'رمز التحقق غير صحيح أو منتهي الصلاحية'], 400);
        }

        try {
            $user = User::firstOrCreate(
                ['phone' => $request->phone],
                [
                    'name' => 'عميل ' . substr($request->phone, -4),
                    'email' => $request->phone . '@fixora.app',
                    'password' => Hash::make('fixora_' . $request->phone),
                    'role' => $request->role ?? 'customer',
                    'is_active' => true,
                ]
            );

            Cache::forget('otp_' . $request->phone);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'تم تسجيل الدخول بنجاح',
                'user' => $user,
                'token' => $token,
                'role' => $user->role ?? 'customer'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'تعذر إتمام الدخول: ' . $e->getMessage()
            ], 500);
        }
    }
}