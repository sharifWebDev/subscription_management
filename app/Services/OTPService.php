<?php

namespace App\Services;

use App\Models\OtpVerification;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OTPService
{
    /**
     * Generate and send OTP to email
     */
    public function generateAndSendOtp(string $email): array
    {
        try {
            // Generate 6-digit OTP
            $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

            // Set expiration (10 minutes)
            $expiresAt = Carbon::now()->addMinutes(10);

            // Delete any existing OTP for this email
            OtpVerification::where('email', $email)->delete();

            // Create new OTP record
            $otpRecord = OtpVerification::create([
                'email' => $email,
                'otp' => Hash::make($otp),
                'expires_at' => $expiresAt,
                'attempts' => 0,
            ]);

            // Send OTP via email
            $this->sendOtpEmail($email, $otp);

            return [
                'success' => true,
                'message' => 'OTP sent successfully',
                'expires_at' => $expiresAt,
            ];

        } catch (Exception $e) {
            \Log::error('OTP generation failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.',
            ];
        }
    }

    /**
     * Verify OTP and create/login user
     */
    public function verifyOtpAndCreateUser(string $email, string $otp, array $userData = []): array
    {
        try {
            // Find OTP record
            $otpRecord = OtpVerification::where('email', $email)
                ->where('expires_at', '>', Carbon::now())
                ->latest()
                ->first();

            if (! $otpRecord) {
                return [
                    'success' => false,
                    'message' => 'OTP expired or not found. Please request a new one.',
                ];
            }

            // Check attempts
            if ($otpRecord->attempts >= 3) {
                return [
                    'success' => false,
                    'message' => 'Too many failed attempts. Please request a new OTP.',
                ];
            }

            // Verify OTP
            if (! Hash::check($otp, $otpRecord->otp)) {
                $otpRecord->increment('attempts');

                return [
                    'success' => false,
                    'message' => 'Invalid OTP. Please try again.',
                ];
            }

            // Find or create user
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $userData['name'] ?? explode('@', $email)[0],
                    'password' => Hash::make(Str::random(16)),
                    'email_verified_at' => Carbon::now(),
                    'preferred_currency' => 'USD',
                    'account_status' => 'active',
                ]
            );

            // Delete used OTP
            $otpRecord->delete();

            // Create login token
            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'success' => true,
                'message' => 'OTP verified successfully',
                'user' => $user,
                'token' => $token,
                'is_new_user' => $user->wasRecentlyCreated,
            ];

        } catch (Exception $e) {
            \Log::error('OTP verification failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Verification failed. Please try again.',
            ];
        }
    }

    /**
     * Send OTP email
     */
    protected function sendOtpEmail(string $email, string $otp): void
    {
        $subject = 'Your Login OTP - '.config('app.name');
        $message = "Your OTP for login is: <strong>{$otp}</strong><br><br>";
        $message .= 'This OTP will expire in 10 minutes.<br>';
        $message .= "If you didn't request this, please ignore this email.";

        Mail::html($message, function ($mail) use ($email, $subject) {
            $mail->to($email)
                ->subject($subject)
                ->from(config('mail.from.address'), config('mail.from.name'));
        });
    }
}
