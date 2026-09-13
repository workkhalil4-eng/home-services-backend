<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $otp = '1234';

        if (app()->environment('production')) {
            // TODO: Replace with real SMS gateway integration (e.g., Twilio, Unifonic, Taqnyat)
            $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            // SMS logic goes here
        }

        Cache::put('otp_' . $request->phone, $otp, now()->addMinutes(5));

        return response()->json([
            'message' => app()->environment('production') 
                ? 'OTP sent successfully' 
                : 'OTP sent successfully (Use 1234 for testing)',
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string',
            'role' => 'required|in:customer,provider'
        ]);

        $cachedOtp = Cache::get('otp_' . $request->phone);

        if ($cachedOtp !== $request->otp) {
            return response()->json(['message' => 'Invalid or expired OTP'], 400);
        }

        $user = User::firstOrCreate(
            ['phone' => $request->phone],
            [
                'name' => 'User ' . substr($request->phone, -4),
                'password' => Hash::make(str()->random(10)),
                'is_active' => true,
                'role' => $request->role, // يتم حفظ هذا فقط عند الإنشاء لأول مرة
            ]
        );

        // Assign role if Spatie permissions are set up, but for now we just return token
        // $user->assignRole($user->role);

        $token = $user->createToken($user->role . '-auth-token')->plainTextToken;

        Cache::forget('otp_' . $request->phone);

        return response()->json([
            'user' => $user,
            'token' => $token,
            'role' => $user->role, // إرجاع الدور الفعلي المحفوظ في قاعدة البيانات
        ]);
    }
}
