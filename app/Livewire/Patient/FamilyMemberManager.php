<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use App\Models\PatientFamilyMember;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FamilyMemberManager extends Component
{
    public bool $showModal = false;

    // Form inputs
    public string $relationship = 'anak';
    public string $nik = '';
    public string $name = '';
    public string $gender = 'L';
    public string $birth_date = '';
    public string $blood_type = '';
    public string $allergies = '';
    public string $phone = '';
    public string $bpjs_number = '';
    public string $address = '';

    protected function rules(): array
    {
        return [
            'relationship' => ['required', 'string', 'in:suami,istri,anak,orang_tua,lainnya'],
            'nik' => ['required', 'digits:16'],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:L,P'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'blood_type' => ['nullable', 'string', 'max:5'],
            'allergies' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'bpjs_number' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function messages(): array
    {
        return [
            'nik.required' => 'NIK anggota keluarga wajib diisi.',
            'nik.digits' => 'NIK harus 16 digit angka.',
            'name.required' => 'Nama anggota keluarga wajib diisi.',
            'birth_date.required' => 'Tanggal lahir wajib diisi.',
            'birth_date.before_or_equal' => 'Tanggal lahir tidak valid.',
        ];
    }

    public function openModal(): void
    {
        $this->reset(['relationship', 'nik', 'name', 'gender', 'birth_date', 'blood_type', 'allergies', 'phone', 'bpjs_number', 'address']);
        $this->relationship = 'anak';
        $this->gender = 'L';
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function save(): void
    {
        $validated = $this->validate();
        $user = Auth::user();

        // 1. Cek apakah pasien dengan NIK ini sudah ada di database RS
        $patient = Patient::where('nik', $this->nik)->first();

        if (! $patient) {
            $patient = Patient::create([
                'user_id' => null, // Anggota keluarga tidak harus punya akun login sendiri
                'medical_record_number' => Patient::generateMedicalRecordNumber(),
                'nik' => $this->nik,
                'name' => $this->name,
                'gender' => $this->gender,
                'birth_date' => $this->birth_date,
                'blood_type' => $this->blood_type ?: null,
                'allergies' => $this->allergies ?: null,
                'phone' => $this->phone ?: $user->phone,
                'bpjs_number' => $this->bpjs_number ?: null,
                'address' => $this->address ?: ($user->patient?->address ?? 'Alamat sama dengan penanggung jawab'),
            ]);
        } else {
            // Update data medis jika ada info baru
            $patient->update([
                'name' => $this->name,
                'gender' => $this->gender,
                'birth_date' => $this->birth_date,
                'blood_type' => $this->blood_type ?: $patient->blood_type,
                'allergies' => $this->allergies ?: $patient->allergies,
                'phone' => $this->phone ?: $patient->phone,
                'bpjs_number' => $this->bpjs_number ?: $patient->bpjs_number,
            ]);
        }

        // 2. Hubungkan ke user di patient_family_members
        PatientFamilyMember::updateOrCreate(
            [
                'user_id' => $user->id,
                'patient_id' => $patient->id,
            ],
            [
                'relationship' => $this->relationship,
            ]
        );

        $this->showModal = false;
        session()->flash('success', "Anggota keluarga {$this->name} berhasil didaftarkan dengan No. RM {$patient->medical_record_number}.");
    }

    public function removeMember(int $id): void
    {
        $member = PatientFamilyMember::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $memberName = $member->patient?->name ?? 'Anggota keluarga';
        $member->delete();

        session()->flash('success', "{$memberName} telah dihapus dari daftar keluarga Anda.");
    }

    public function render()
    {
        $familyMembers = PatientFamilyMember::with('patient')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('livewire.patient.family-member-manager', [
            'familyMembers' => $familyMembers,
            'primaryPatient' => Auth::user()->patient,
        ]);
    }
}
