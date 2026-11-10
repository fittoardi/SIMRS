<?php

namespace App\Livewire\Public;

use App\Enums\BedStatus;
use App\Models\Room;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Url;
use Livewire\Component;

class BedAvailabilityBoard extends Component
{
    #[Url]
    public string $selectedClass = '';

    public function render(): View
    {
        $cacheKey = 'public_bed_availability_'.($this->selectedClass ?: 'all');

        $rooms = Cache::remember($cacheKey, 15, function () {
            $query = Room::query()
                ->with(['beds' => function ($q) {
                    $q->orderBy('bed_number');
                }])
                ->where('is_active', true);

            if (! empty($this->selectedClass)) {
                $query->where('class_type', $this->selectedClass);
            }

            return $query->orderBy('class_type')->orderBy('name')->get();
        });

        // Calculate global statistics
        $allRooms = Room::with('beds')->where('is_active', true)->get();
        $allBeds = $allRooms->flatMap->beds;

        $totalCount = $allBeds->count();
        $availableCount = $allBeds->where('status', BedStatus::Available)->count();
        $occupiedCount = $allBeds->where('status', BedStatus::Occupied)->count();
        $cleaningCount = $allBeds->where('status', BedStatus::Cleaning)->count();
        $maintenanceCount = $allBeds->where('status', BedStatus::Maintenance)->count();

        $bor = $totalCount > 0 ? round(($occupiedCount / $totalCount) * 100, 1) : 0;

        return view('livewire.public.bed-availability-board', [
            'rooms' => $rooms,
            'totalCount' => $totalCount,
            'availableCount' => $availableCount,
            'occupiedCount' => $occupiedCount,
            'cleaningCount' => $cleaningCount,
            'maintenanceCount' => $maintenanceCount,
            'bor' => $bor,
        ]);
    }
}
