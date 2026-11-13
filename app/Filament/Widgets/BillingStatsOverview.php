<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Models\Invoice;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class BillingStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return $user->hasAnyRole(['super_admin', 'cashier']);
    }

    protected function getStats(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        $todayRevenue = (float) Payment::where('status', PaymentStatus::Paid)
            ->whereDate('paid_at', $today)
            ->sum('amount');

        $monthRevenue = (float) Payment::where('status', PaymentStatus::Paid)
            ->where('paid_at', '>=', $thisMonth)
            ->sum('amount');

        $unpaidInvoicesCount = Invoice::where('status', PaymentStatus::Unpaid)->count();
        $unpaidInvoicesTotal = (float) Invoice::where('status', PaymentStatus::Unpaid)->sum('total');

        $paidTodayCount = Invoice::where('status', PaymentStatus::Paid)
            ->whereDate('updated_at', $today)
            ->count();

        return [
            Stat::make('Penerimaan Kasir Hari Ini', 'Rp ' . number_format($todayRevenue, 0, ',', '.'))
                ->description('Total kas masuk tunai & digital')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Tagihan Belum Lunas', $unpaidInvoicesCount . ' Invoice')
                ->description('Total piutang: Rp ' . number_format($unpaidInvoicesTotal, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-clock')
                ->color($unpaidInvoicesCount > 0 ? 'warning' : 'gray'),

            Stat::make('Transaksi Selesai Hari Ini', $paidTodayCount . ' Invoice')
                ->description('Akumulasi bulan ini: Rp ' . number_format($monthRevenue, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('primary'),
        ];
    }
}

