<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use App\PendingRegistration;
use App\Notifications\RegistrationTokenNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RegisterController extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Step 1: Show registration form (name, username, email)
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Step 1: Handle initial registration - send verification token
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:users', 'unique:pending_registrations'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'unique:pending_registrations'],
        ], [
            'name.required' => 'Nama harus diisi.',
            'username.required' => 'Username harus diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, dash, dan underscore.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Delete old pending registrations with same email/username
        PendingRegistration::where('email', $request->email)
            ->orWhere('username', $request->username)
            ->delete();

        // Generate token
        $token = PendingRegistration::generateToken();

        // Create pending registration
        $pending = PendingRegistration::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'token' => $token,
            'expires_at' => Carbon::now()->addMinutes(30),
        ]);

        // Send email with token
        Notification::route('mail', $request->email)
            ->notify(new RegistrationTokenNotification($token, $request->name));

        // Redirect to verification page
        return redirect()->route('register.verify.form', ['email' => $request->email])
            ->with('status', 'Kode verifikasi telah dikirim ke email Anda.');
    }

    /**
     * Step 2: Show token verification form
     */
    public function showVerifyForm(Request $request)
    {
        $email = $request->query('email');
        
        if (!$email) {
            return redirect()->route('register')
                ->with('error', 'Email tidak ditemukan.');
        }

        $pending = PendingRegistration::where('email', $email)
            ->where('is_verified', false)
            ->first();

        if (!$pending) {
            return redirect()->route('register')
                ->with('error', 'Registrasi tidak ditemukan. Silakan daftar ulang.');
        }

        if ($pending->isExpired()) {
            $pending->delete();
            return redirect()->route('register')
                ->with('error', 'Kode verifikasi sudah kadaluarsa. Silakan daftar ulang.');
        }

        return view('auth.verify-token', compact('email'));
    }

    /**
     * Step 2: Handle token verification
     */
    public function verifyToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'token' => ['required', 'string', 'size:6'],
        ], [
            'token.required' => 'Kode verifikasi harus diisi.',
            'token.size' => 'Kode verifikasi harus 6 digit.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $pending = PendingRegistration::where('email', $request->email)
            ->where('is_verified', false)
            ->first();

        if (!$pending) {
            return redirect()->route('register')
                ->with('error', 'Registrasi tidak ditemukan.');
        }

        if ($pending->isExpired()) {
            $pending->delete();
            return redirect()->route('register')
                ->with('error', 'Kode verifikasi sudah kadaluarsa. Silakan daftar ulang.');
        }

        if ($pending->token !== $request->token) {
            return redirect()->back()
                ->withErrors(['token' => 'Kode verifikasi tidak valid.'])
                ->withInput();
        }

        // Mark as verified
        $pending->is_verified = true;
        $pending->save();

        // Redirect to set password form
        return redirect()->route('register.password.form', ['email' => $request->email])
            ->with('status', 'Email berhasil diverifikasi! Silakan buat password.');
    }

    /**
     * Step 3: Show set password form
     */
    public function showPasswordForm(Request $request)
    {
        $email = $request->query('email');
        
        if (!$email) {
            return redirect()->route('register')
                ->with('error', 'Email tidak ditemukan.');
        }

        $pending = PendingRegistration::where('email', $email)
            ->where('is_verified', true)
            ->first();

        if (!$pending) {
            return redirect()->route('register')
                ->with('error', 'Registrasi tidak ditemukan atau belum diverifikasi.');
        }

        return view('auth.set-password', [
            'email' => $email,
            'name' => $pending->name,
        ]);
    }

    /**
     * Step 3: Handle password creation and final registration
     */
    public function setPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $pending = PendingRegistration::where('email', $request->email)
            ->where('is_verified', true)
            ->first();

        if (!$pending) {
            return redirect()->route('register')
                ->with('error', 'Registrasi tidak ditemukan.');
        }

        // Create the user
        $user = User::create([
            'name' => $pending->name,
            'username' => $pending->username,
            'email' => $pending->email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
        ]);

        // Delete pending registration
        $pending->delete();

        // Login the user
        Auth::login($user);

        // Redirect to dashboard
        return redirect()->route('dashboard.customer')
            ->with('status', 'Selamat! Akun Anda berhasil dibuat.');
    }

    /**
     * Resend verification token
     */
    public function resendToken(Request $request)
    {
        $email = $request->email;

        $pending = PendingRegistration::where('email', $email)
            ->where('is_verified', false)
            ->first();

        if (!$pending) {
            return redirect()->route('register')
                ->with('error', 'Registrasi tidak ditemukan.');
        }

        // Generate new token
        $token = PendingRegistration::generateToken();
        $pending->token = $token;
        $pending->expires_at = Carbon::now()->addMinutes(30);
        $pending->save();

        // Send email with new token
        Notification::route('mail', $email)
            ->notify(new RegistrationTokenNotification($token, $pending->name));

        return redirect()->route('register.verify.form', ['email' => $email])
            ->with('status', 'Kode verifikasi baru telah dikirim ke email Anda.');
    }
}
