<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use App\Models\Goat;
use App\Helpers\CurrencyHelper;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FatteningProfitStats extends BaseWidget
{
    protected static ?int $sort = 5;

    protected function getStats(): array
    {
        // Fattening sales data
        $fatteningSales = Sale::whereHas('goat', function ($query) {
            $query->where('type', 'fattening');
        });

        $totalFatteningSales = $fatteningSales->count();
        $totalRevenue = $fatteningSales->sum('sale_price');
        $totalCost = $fatteningSales->sum('cost_price');
        $totalProfit = $totalRevenue - $totalCost;

        // Monthly data
        $monthlyFatteningSales = Sale::whereHas('goat', function ($query) {
            $query->where('type', 'fattening');
        })->whereMonth('sale_date', now()->month);

        $monthlyRevenue = $monthlyFatteningSales->sum('sale_price');
        $monthlyCost = $monthlyFatteningSales->sum('cost_price');
        $monthlyProfit = $monthlyRevenue - $monthlyCost;

        // Average profit per goat
        $avgProfit = $totalFatteningSales > 0 ? $totalProfit / $totalFatteningSales : 0;

        // Profit margin percentage
        $profitMargin = $totalCost > 0 ? ($totalProfit / $totalCost) * 100 : 0;

        // Active fattening goats
        $activeFatteningGoats = Goat::where('type', 'fattening')
            ->where('status', 'active')
            ->count();

        // Investment in active goats (using purchase_price)
        $activeInvestment = Goat::where('type', 'fattening')
            ->where('status', 'active')
            ->sum('purchase_price');

        return [
            Stat::make('Total Keuntungan Penggemukan', CurrencyHelper::formatRupiah($totalProfit))
                ->description('Dari ' . $totalFatteningSales . ' penjualan')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($totalProfit >= 0 ? 'success' : 'danger'),

            Stat::make('Keuntungan Bulan Ini', CurrencyHelper::formatRupiah($monthlyProfit))
                ->description('Dari ' . $monthlyFatteningSales->count() . ' penjualan')
                ->descriptionIcon('heroicon-m-calendar')
                ->color($monthlyProfit >= 0 ? 'success' : 'danger'),

            Stat::make('Rata-rata Keuntungan', CurrencyHelper::formatRupiah($avgProfit))
                ->description('Per ekor kambing penggemukan')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info'),

            Stat::make('Margin Keuntungan', number_format($profitMargin, 1) . '%')
                ->description('Persentase keuntungan dari modal')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($profitMargin >= 20 ? 'success' : ($profitMargin >= 10 ? 'warning' : 'danger')),

            Stat::make('Kambing Aktif Penggemukan', $activeFatteningGoats)
                ->description('Investasi: ' . CurrencyHelper::formatRupiah($activeInvestment))
                ->descriptionIcon('heroicon-m-heart')
                ->color('warning'),

            Stat::make('ROI Penggemukan', $totalCost > 0 ? number_format(($totalProfit / $totalCost) * 100, 1) . '%' : '0%')
                ->description('Return on Investment')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($profitMargin >= 30 ? 'success' : ($profitMargin >= 15 ? 'warning' : 'danger')),
        ];
    }
}
