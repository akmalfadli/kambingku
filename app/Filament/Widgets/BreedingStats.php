<?php

namespace App\Filament\Widgets;

use App\Models\Goat;
use App\Models\Pregnancy;
use App\Models\KiddingRecord;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BreedingStats extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';
    protected static ?int $sort = 6;

    protected function getStats(): array
    {
        $currentYear = now()->year;

        return [
            Stat::make('Breeding Females', Goat::where('gender', 'female')->where('type', 'breeding')->where('status', 'active')->count())
                ->description('Active breeding does')
                ->descriptionIcon('heroicon-m-heart')
                ->color('info'),

            Stat::make('Breeding Males', Goat::where('gender', 'male')->where('type', 'breeding')->where('status', 'active')->count())
                ->description('Active breeding bucks')
                ->descriptionIcon('heroicon-m-user')
                ->color('primary'),

            Stat::make('Pregnant Does', Pregnancy::where('status', 'pregnant')->count())
                ->description('Currently pregnant')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('warning'),

            Stat::make('Kids Born This Year', KiddingRecord::whereYear('delivery_date', $currentYear)->sum('number_of_kids'))
                ->description('Total offspring in ' . $currentYear)
                ->descriptionIcon('heroicon-m-gift')
                ->color('success'),

            Stat::make('Average Litter Size',
                number_format(
                    KiddingRecord::whereYear('delivery_date', $currentYear)->avg('number_of_kids') ?? 0,
                    1
                )
            )
                ->description('Kids per delivery this year')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info'),

            Stat::make('Deliveries Due Soon',
                Pregnancy::where('status', 'pregnant')
                    ->whereBetween('expected_delivery_date', [now(), now()->addDays(30)])
                    ->count()
            )
                ->description('Due in next 30 days')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
        ];
    }
}
