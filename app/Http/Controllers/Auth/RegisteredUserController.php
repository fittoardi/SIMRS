<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    public function __construct(
        protected OtpService $otpService
    ) {}

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register', [
            'pending' => session('pending_registration', []),
        ]);
    }

    /**
     * Handle an incoming registration request: Validasi input & kirim kode OTP.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone' => ['required', 'string', 'min:9', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nik' => ['nullable', 'digits:16', 'unique:patients,nik'],
            'gender' => ['nullable', 'in:L,P'],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'blood_type' => ['nullable', 'string', 'max:5'],
            'allergies' => ['nullable', 'string', 'max:500'],
            'bpjs_number' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'emergency_contact_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:50'],
        ], [
            'phone.required' => 'Nomor WhatsApp wajib diisi untuk verifikasi OTP.',
            'phone.min' => 'Nomor WhatsApp minimal 9 digit.',
            'nik.digits' => 'NIK harus berjumlah tepat 16 digit angka.',
            'nik.unique' => 'NIK ini telah terdaftar dalam sistem rekam medis.',
        ]);

        // Simpan data pendaftaran sementara ke session
        session(['pending_registration' => $validated]);

        // Kirim kode OTP WhatsApp melalui OtpService
        $sendResult = $this->otpService->sendOtp($validated['phone'], 'register');

        if (! $sendResult['success']) {
            return back()->withInput()->withErrors(['phone' => $sendResult['message']]);
        }

        return redirect()->route('register.otp')->with('success', 'Kode OTP 6-digit telah dikirimkan ke nomor WhatsApp Anda.');
    }

    /**
     * Tampilkan halaman verifikasi OTP pendaftaran.
     */
    public function showOtp(): View|RedirectResponse
    {
        $pending = session('pending_registration');
        if (! $pending) {
            return redirect()->route('register')->with('warning', 'Sesi pendaftaran telah berakhir. Silakan isi kembali formulir.');
        }

        $phone = $pending['phone'];
        $cleanPhone = preg_replace('/\D/', '', $phone) ?? '';
        $maskedPhone = (strlen($cleanPhone) >= 8)
            ? substr($cleanPhone, 0, 4) . '-****-' . substr($cleanPhone, -4)
            : $phone;

        return view('auth.register-otp', [
            'phone' => $phone,
            'maskedPhone' => $maskedPhone,
        ]);
    }

    /**
     * Verifikasi kode OTP dan selesaikan pembuatan akun & rekam medis pasien.
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $pending = session('pending_registration');
        if (! $pending) {
            return redirect()->route('register')->with('error', 'Sesi pendaftaran telah kadaluarsa. Silakan ulangi pengisian.');
        }

        $request->validate([
            'otp_code' => ['required', 'string', 'size:6'],
        ], [
            'otp_code.required' => 'Silakan masukkan 6 digit kode OTP.',
            'otp_code.size' => 'Kode OTP harus tepat 6 digit angka.',
        ]);

        $verification = $this->otpService->verifyOtp($pending['phone'], $request->otp_code, 'register');

        if (! $verification['valid']) {
            return back()->withErrors(['otp_code' => $verification['message']]);
        }

        // 1. Buat User terverifikasi
        $user = User::create([
            'name' => $pending['name'],
            'email' => $pending['email'],
            'phone' => $pending['phone'],
            'password' => Hash::make($pending['password']),
            'is_active' => true,
        ]);

        // 2. Berikan role patient
        Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
        $user->assignRole('patient');

        // 3. Buatkan profil Rekam Medis (Patient)
        Patient::create([
            'user_id' => $user->id,
            'medical_record_number' => Patient::generateMedicalRecordNumber(),
            'nik' => $pending['nik'] ?? ('3171' . str_pad((string) $user->id, 12, '0', STR_PAD_LEFT)),
            'name' => $user->name,
            'gender' => $pending['gender'] ?? 'L',
            'birth_date' => $pending['birth_date'] ?? '1995-01-01',
            'blood_type' => $pending['blood_type'] ?? null,
            'allergies' => $pending['allergies'] ?? null,
            'bpjs_number' => $pending['bpjs_number'] ?? null,
            'phone' => $pending['phone'],
            'address' => $pending['address'] ?? 'Alamat belum dilengkapi',
            'emergency_contact_name' => $pending['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $pending['emergency_contact_phone'] ?? null,
            'emergency_contact_relation' => $pending['emergency_contact_relation'] ?? null,
        ]);

        // Hapus session pendaftaran sementara
        session()->forget('pending_registration');

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('patient.dashboard', absolute: false))->with('success', 'Pendaftaran akun berhasil! Nomor WhatsApp Anda telah terverifikasi resmi.');
    }

    /**
     * Kirim ulang kode OTP WhatsApp.
     */
    public function resendOtp(): RedirectResponse
    {
        $pending = session('pending_registration');
        if (! $pending) {
            return redirect()->route('register')->with('error', 'Sesi pendaftaran telah berakhir. Silakan ulangi.');
        }

        $result = $this->otpService->sendOtp($pending['phone'], 'register');

        if (! $result['success']) {
            return back()->with('warning', $result['message']);
        }

        return back()->with('success', 'Kode OTP baru telah berhasil dikirimkan ke WhatsApp Anda.');
    }
}
