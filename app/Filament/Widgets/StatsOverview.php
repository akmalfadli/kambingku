<?php

namespace App\Filament\Widgets;

use App\Models\Goat;
use App\Models\Sale;
use App\Models\Pregnancy;
use App\Models\Expense;
use App\Helpers\CurrencyHelper;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Kambing', Goat::count())
                ->description('Aktif: ' . Goat::where('status', 'active')->count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Kambing Breeding', Goat::where('type', 'breeding')->where('status', 'active')->count())
                ->description('Penggemukan: ' . Goat::where('type', 'fattening')->where('status', 'active')->count())
                ->descriptionIcon('heroicon-m-heart')
                ->color('info'),

            Stat::make('Kambing Hamil', Pregnancy::where('status', 'pregnant')->count())
                ->description('Melahirkan bulan ini: ' . Pregnancy::where('status', 'pregnant')
                    ->whereBetween('expected_delivery_date', [now()->startOfMonth(), now()->endOfMonth()])
                    ->count())
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('warning'),

            Stat::make('Pendapatan Bulanan', CurrencyHelper::formatRupiah(Sale::whereMonth('sale_date', now()->month)->sum('sale_price')))
                ->description('Keuntungan Bulanan: ' . CurrencyHelper::formatRupiah(
                    Sale::whereMonth('sale_date', now()->month)->get()->sum('profit')
                ))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Pengeluaran Bulanan', CurrencyHelper::formatRupiah(Expense::whereMonth('expense_date', now()->month)->sum('amount')))
                ->description('Biaya pakan: ' . CurrencyHelper::formatRupiah(
                    Expense::where('expense_type', 'feed')->whereMonth('expense_date', now()->month)->sum('amount')
                ))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Hewan Terjual', Sale::whereMonth('sale_date', now()->month)->count())
                ->description('Total penjualan: ' . Sale::count())
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info'),
        ];
    }
}
