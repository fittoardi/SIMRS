<?php

namespace App\Livewire\Public;

use App\Models\Doctor;
use App\Models\Polyclinic;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Url;
use Livewire\Component;

class DoctorScheduleFilter extends Component
{
    #[Url]
    public string $search = '';

    #[Url]
    public string $selectedPolyclinic = '';

    #[Url]
    public string $selectedDay = '';

    public function resetFilters(): void
    {
        $this->search = '';
        $this->selectedPolyclinic = '';
        $this->selectedDay = '';
    }

    public function render(): View
    {
        $cacheKey = 'public_polyclinics_list';
        $polyclinics = Cache::remember($cacheKey, 60, function () {
            return Polyclinic::where('is_active', true)->orderBy('name')->get();
        });

        $query = Doctor::query()
            ->with(['polyclinic', 'schedules' => function ($q) {
                $q->where('is_active', true)->orderBy('day_of_week')->orderBy('start_time');
            }])
            ->where('is_active', true);

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('specialization', 'like', '%'.$this->search.'%');
            });
        }

        if (! empty($this->selectedPolyclinic)) {
            $query->where('polyclinic_id', $this->selectedPolyclinic);
        }

        if (! empty($this->selectedDay)) {
            $query->whereHas('schedules', function ($q) {
                $q->where('day_of_week', $this->selectedDay)
                    ->where('is_active', true);
            });
        }

        $doctors = $query->orderBy('name')->get();

        return view('livewire.public.doctor-schedule-filter', [
            'doctors' => $doctors,
            'polyclinics' => $polyclinics,
        ]);
    }
}
