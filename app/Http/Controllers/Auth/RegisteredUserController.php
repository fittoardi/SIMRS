<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:20'],
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
            'nik.digits' => 'NIK harus berjumlah tepat 16 digit angka.',
            'nik.unique' => 'NIK ini telah terdaftar dalam sistem rekam medis.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        // Berikan role patient (pastikan role ada di database)
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
        $user->assignRole('patient');

        // Otomatis buatkan profil Rekam Medis (Patient)
        Patient::create([
            'user_id' => $user->id,
            'medical_record_number' => Patient::generateMedicalRecordNumber(),
            'nik' => $request->nik ?: '3171'.str_pad((string) $user->id, 12, '0', STR_PAD_LEFT),
            'name' => $user->name,
            'gender' => $request->gender ?: 'L',
            'birth_date' => $request->birth_date ?: '1995-01-01',
            'blood_type' => $request->blood_type ?: null,
            'allergies' => $request->allergies ?: null,
            'bpjs_number' => $request->bpjs_number ?: null,
            'phone' => $request->phone ?: null,
            'address' => $request->address ?: 'Alamat belum dilengkapi',
            'emergency_contact_name' => $request->emergency_contact_name ?: null,
            'emergency_contact_phone' => $request->emergency_contact_phone ?: null,
            'emergency_contact_relation' => $request->emergency_contact_relation ?: null,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('patient.dashboard', absolute: false));
    }
}
