<?php

namespace App\Filament\Widgets;

use App\Enums\BedStatus;
use App\Models\Bed;
use App\Models\Patient;
use App\Models\Visit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class HospitalStatsOverview extends BaseWidget
{
    protected static ?int $sort = 0;

    public static function canView(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return $user->hasAnyRole(['super_admin', 'admission', 'doctor', 'nurse']);
    }

    protected function getStats(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        $visitsToday = Visit::whereDate('visit_date', $today)->count();
        $visitsThisMonth = Visit::where('visit_date', '>=', $thisMonth)->count();

        $totalBeds = Bed::whereHas('room', fn ($q) => $q->where('is_active', true))->count();
        $occupiedBeds = Bed::where('status', BedStatus::Occupied)
            ->whereHas('room', fn ($q) => $q->where('is_active', true))
            ->count();

        $bor = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 1) : 0;
        $totalPatients = Patient::count();

        return [
            Stat::make('Kunjungan Pasien Hari Ini', $visitsToday.' Pasien')
                ->description('Bulan ini: '.$visitsThisMonth.' kunjungan terdata')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Bed Occupancy Rate (BOR)', $bor.'%')
                ->description("{$occupiedBeds} dari {$totalBeds} tempat tidur terisi")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color($bor >= 85 ? 'danger' : ($bor >= 60 ? 'success' : 'info')),

            Stat::make('Total Pasien Terdaftar', number_format($totalPatients, 0, ',', '.').' Jiwa')
                ->description('Database No. RM RS Aisyah Medika')
                ->descriptionIcon('heroicon-m-identification')
                ->color('success'),
        ];
    }
}
