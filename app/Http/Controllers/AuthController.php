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

class AuthController extends Controller
{
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
