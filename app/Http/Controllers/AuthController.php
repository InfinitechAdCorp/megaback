<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountApprovedMail;
use App\Mail\VerificationMail;
use Illuminate\Support\Facades\Log;


use App\Models\Otp;
use Carbon\Carbon;

class AuthController extends Controller
{
public function sendOtp(Request $request) {
    // Check if the email exists before validating
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => 'Email not registered.'], 404);
    }

    // Validate email format (removing 'exists' rule)
    $request->validate([
        'email' => 'required|email'
    ]);

    // Check if user is verified
    if (!$user->is_verified) {
        return response()->json(['message' => 'Email is not verified. Please verify your email first.'], 400);
    }

    // Generate OTP
    $otp = rand(100000, 999999);

    // Store OTP in the database
    Otp::updateOrCreate(
        ['email' => $request->email],
        ['otp' => $otp, 'expires_at' => Carbon::now()->addMinutes(5)]
    );

    // Send OTP via email
    try {
        Mail::raw("Your OTP for password reset is: $otp", function ($message) use ($request) {
            $message->to($request->email)->subject('Password Reset OTP');
        });

        return response()->json(['message' => 'OTP sent to your email']);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Failed to send OTP. Try again later.'], 500);
    }
}


    // 🔹 VERIFY OTP
    public function verifyOtp(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        // Find OTP record
        $otpRecord = Otp::where('email', $request->email)->where('otp', $request->otp)->first();

        // Check if OTP exists and is still valid
        if (!$otpRecord || Carbon::now()->greaterThan($otpRecord->expires_at)) {
            return response()->json(['message' => 'Invalid or expired OTP'], 400);
        }

        // Mark OTP as verified (optional: delete it after verification)
        return response()->json(['message' => 'OTP verified successfully']);
    }

    // 🔹 RESET PASSWORD
    public function resetPassword(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6',
            'password' => 'required|min:6',
        ]);

        // Check OTP
        $otpRecord = Otp::where('email', $request->email)->where('otp', $request->otp)->first();

        if (!$otpRecord || Carbon::now()->greaterThan($otpRecord->expires_at)) {
            return response()->json(['message' => 'Invalid or expired OTP'], 400);
        }

        // Reset password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete OTP after successful reset
        $otpRecord->delete();

        return response()->json(['message' => 'Password reset successfully']);
    }
public function register(Request $request)
{
    // Validate user input
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users', // Ensures unique emails
        'password' => 'required|string|min:6',
    ]);

    // Create user account
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'is_verified' => false, // Requires admin approval
    ]);

    // Generate verification link
    $verificationUrl = url("/api/verification/{$user->id}");

    // Log registration details
    Log::info("New user registration request:", [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'verification_link' => $verificationUrl,
    ]);

    try { 
        // Send verification email to the admin (NOT the user)
        Mail::to('roniversondelmundo@gmail.com')->send(new VerificationMail($user, $verificationUrl));

        Log::info("Verification email sent to admin: roniversondelmundo@gmail.com");

    } catch (\Exception $e) {
        Log::error("Email sending failed: " . $e->getMessage());
        return response()->json(['message' => 'Registration successful, but email failed to send.'], 500);
    }

    return response()->json([
        'message' => 'Registration request sent. Waiting for admin approval.',
    ], 201);
}



public function login(Request $request)
{
    Log::info('🔹 Login attempt detected.', ['email' => $request->email]);

    // Validate input
    $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string|min:6',
    ]);

    // Find user by email
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        Log::warning('⚠️ Login failed: User not found.', ['email' => $request->email]);
        return response()->json(['message' => 'User not found. Please register first.'], 404);
    }

    // Check if password is correct
    if (!Hash::check($request->password, $user->password)) {
        Log::warning('⚠️ Login failed: Invalid credentials.', ['email' => $request->email]);
        return response()->json(['message' => 'Invalid credentials.'], 401);
    }

    // Check if user is verified
    if (!$user->is_verified) {
        Log::warning('⛔ Login blocked: User not verified.', ['email' => $request->email]);
        return response()->json(['message' => 'Account not verified. Please wait for admin approval.'], 403);
    }

    // Generate token
    $token = $user->createToken('auth_token')->plainTextToken;

    Log::info('✅ Login successful.', [
        'user_id' => $user->id,
        'email' => $user->email,
        'token_generated' => now(),
    ]);

    return response()->json([
        'message' => 'Login successful',
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ],
        'token' => $token,
    ], 200);
}


}
