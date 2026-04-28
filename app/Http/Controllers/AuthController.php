<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|max:15',
            'password' => 'required|min:6|confirmed',
        ]);

        $otp = rand(100000, 999999);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(15),
            'is_active' => false,
        ]);

        Mail::raw("Your activation code is: $otp", function($m) use ($user) {
            $m->to($user->email)->subject('Activate Your Account');
        });

        return redirect()->route('verify.page', ['email' => $user->email])
                        ->with('success', 'Check your email for the OTP.');
    }

    // 2. Handle OTP Verification
    public function verifyOtp(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)
                    ->where('otp', $request->otp)
                    ->where('otp_expires_at', '>', Carbon::now())
                    ->first();

        if (!$user) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        $user->update(['is_active' => true, 'otp' => null, 'otp_expires_at' => null]);
        auth()->login($user);

        return redirect()->route('dashboard');
    }
    // Show Login
    public function showLogin() {
        if (Auth::check()) return redirect()->route('dashboard');
        return view('auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if user is active
            if (!$user->is_active) {
                // If not active, we don't want them logged in yet
                // Or you can keep them logged in but force the OTP view
                return redirect()->route('verify.page', ['email' => $user->email])
                                ->with('error', 'Please activate your account with the OTP sent to your email.');
            }

            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
    // 1. Show the email entry form
    public function showForgotForm() {
        return view('auth.forgot-password');
    }

    // 2. Generate token and send email
    public function sendResetLink(Request $request) {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $token = Str::random(64);

        // Insert or update token in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        Mail::raw("Click the link below to reset your password:\n" . route('password.reset', $token), function($m) use ($request) {
            $m->to($request->email)->subject('Reset Password Notification');
        });

        return back()->with('status', 'We have emailed your password reset link!');
    }

    // 3. Show the new password entry form
    public function showResetForm($token) {
        return view('auth.reset-password', ['token' => $token]);
    }

    // 4. Update the password and delete the token
    public function resetPassword(Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Check if token is valid
        $reset = DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->where('token', $request->token)
                    ->first();

        if (!$reset) {
            return back()->withErrors(['email' => 'Invalid token or email.']);
        }
        // 2. CHECK EXPIRATION (e.g., 60 minutes)
        $expiresAt = \Carbon\Carbon::parse($reset->created_at)->addMinutes(60);
        
        if (\Carbon\Carbon::now()->gt($expiresAt)) {
            // Token is expired, delete it and kick them back
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return redirect()->route('password.request')->withErrors(['email' => 'The reset link has expired. Please request a new one.']);
        }
        // Update User
        $user = \App\Models\User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        // Delete the token so it can't be used again
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password reset successfully!');
    }
    public function resendOtp(Request $request) {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();

        // Check if user is already active
        if ($user->is_active) {
            return redirect()->route('login')->with('info', 'Account already active. Please login.');
        }

        // Generate new OTP and expiry
        $otp = rand(100000, 999999);
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => \Carbon\Carbon::now()->addMinutes(15)
        ]);

        // Send Mail
        Mail::raw("Your new activation code is: $otp", function($m) use ($user) {
            $m->to($user->email)->subject('Resend: Activate Your Account');
        });

        return back()->with('success', 'A new OTP has been sent to your email.');
    }
}