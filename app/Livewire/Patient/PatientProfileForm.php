<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class PatientProfileForm extends Component
{
    public ?int $patientId = null;
    public string $name = '';
    public string $nik = '';
    public string $gender = 'L';
    public string $birth_date = '';
    public string $blood_type = '';
    public string $allergies = '';
    public string $bpjs_number = '';
    public string $insurance_name = '';
    public string $insurance_number = '';
    public string $phone = '';
    public string $address = '';
    public string $emergency_contact_name = '';
    public string $emergency_contact_phone = '';
    public string $emergency_contact_relation = '';

    public ?string $medical_record_number = null;
    public bool $isSaved = false;

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name ?? '';
        $this->phone = $user->phone ?? '';

        $patient = $user->patient;
        if ($patient) {
            $this->patientId = $patient->id;
            $this->name = $patient->name;
            $this->nik = $patient->nik ?? '';
            $this->gender = $patient->gender ?? 'L';
            $this->birth_date = $patient->birth_date ? $patient->birth_date->format('Y-m-d') : '';
            $this->blood_type = $patient->blood_type ?? '';
            $this->allergies = $patient->allergies ?? '';
            $this->bpjs_number = $patient->bpjs_number ?? '';
            $this->insurance_name = $patient->insurance_name ?? '';
            $this->insurance_number = $patient->insurance_number ?? '';
            $this->phone = $patient->phone ?? $user->phone ?? '';
            $this->address = $patient->address ?? '';
            $this->emergency_contact_name = $patient->emergency_contact_name ?? '';
            $this->emergency_contact_phone = $patient->emergency_contact_phone ?? '';
            $this->emergency_contact_relation = $patient->emergency_contact_relation ?? '';
            $this->medical_record_number = $patient->medical_record_number;
        }
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'nik' => [
                'required',
                'digits:16',
                Rule::unique('patients', 'nik')->ignore($this->patientId),
            ],
            'gender' => ['required', 'in:L,P'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'blood_type' => ['nullable', 'string', 'max:5'],
            'allergies' => ['nullable', 'string', 'max:500'],
            'bpjs_number' => ['nullable', 'string', 'max:30'],
            'insurance_name' => ['nullable', 'string', 'max:100'],
            'insurance_number' => ['nullable', 'string', 'max:50'],
            'phone' => ['required', 'string', 'min:9', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'emergency_contact_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:50'],
        ];
    }

    protected function messages(): array
    {
        return [
            'nik.required' => 'Nomor Induk Kependudukan (NIK) wajib diisi.',
            'nik.digits' => 'NIK harus berjumlah tepat 16 digit angka.',
            'nik.unique' => 'NIK ini telah terdaftar dalam sistem pasien.',
            'name.required' => 'Nama lengkap pasien wajib diisi.',
            'birth_date.required' => 'Tanggal lahir wajib diisi.',
            'birth_date.before_or_equal' => 'Tanggal lahir tidak boleh di masa mendatang.',
            'phone.required' => 'Nomor HP aktif wajib diisi untuk notifikasi antrean.',
            'address.required' => 'Alamat domisili lengkap wajib diisi.',
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        $user = Auth::user();

        if ($this->patientId) {
            $patient = Patient::findOrFail($this->patientId);
            $patient->update($validated);
        } else {
            $validated['user_id'] = $user->id;
            $validated['medical_record_number'] = Patient::generateMedicalRecordNumber();
            $patient = Patient::create($validated);
            $this->patientId = $patient->id;
            $this->medical_record_number = $patient->medical_record_number;
        }

        // Sinkronisasi nama/nomor hp user jika ada perubahan
        if ($user->phone !== $this->phone) {
            $user->update(['phone' => $this->phone]);
        }

        $this->isSaved = true;
        session()->flash('success', 'Profil data rekam medis pasien berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.patient.patient-profile-form');
    }
}
