<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\FeedingLog;
use App\Models\HealthRecord;
use Filament\Widgets\ChartWidget;

class ExpenseBreakdown extends ChartWidget
{
    protected static ?string $heading = 'Rincian Pengeluaran Bulanan';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $feedExpenses = FeedingLog::whereMonth('feeding_date', $currentMonth)
            ->whereYear('feeding_date', $currentYear)
            ->sum('cost');

        $healthExpenses = HealthRecord::whereMonth('record_date', $currentMonth)
            ->whereYear('record_date', $currentYear)
            ->sum('cost');

        $otherExpenses = Expense::whereMonth('expense_date', $currentMonth)
            ->whereYear('expense_date', $currentYear)
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'data' => [$feedExpenses, $healthExpenses, $otherExpenses],
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 205, 86, 0.8)',
                    ],
                ],
            ],
            'labels' => ['Feed', 'Health', 'Other'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
