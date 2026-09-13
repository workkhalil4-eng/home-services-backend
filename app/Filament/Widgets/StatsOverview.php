<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\ServiceRequest;
use App\Models\ProviderProfile;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalRevenue = ServiceRequest::where('payment_status', 'paid')->sum('total_price');
        $totalCompleted = ServiceRequest::where('status', 'completed')->count();
        $totalPendingProviders = ProviderProfile::where('kyc_status', 'pending')->count();

        return [
            Stat::make('Total Revenue', number_format($totalRevenue, 2) . ' SAR')
                ->description('Total captured payments')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
                
            Stat::make('Completed Requests', $totalCompleted)
                ->description('Successfully finished services')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary'),
                
            Stat::make('Pending KYC Providers', $totalPendingProviders)
                ->description('Requires admin approval')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('warning'),
        ];
    }
}
