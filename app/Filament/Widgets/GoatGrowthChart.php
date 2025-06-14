<?php

namespace App\Filament\Widgets;

use App\Models\WeightLog;
use App\Models\Goat;
use Filament\Widgets\ChartWidget;
use Filament\Forms\Components\Select;

class GoatGrowthChart extends ChartWidget
{
    protected static ?string $heading = 'Goat Growth Chart';
    protected static ?int $sort = 5;

    public ?string $filter = null;

    protected function getFilters(): ?array
    {
        return Goat::where('status', 'active')
            ->pluck('tag_number', 'id')
            ->toArray();
    }

    protected function getData(): array
    {
        if (!$this->filter) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $weightLogs = WeightLog::where('goat_id', $this->filter)
            ->orderBy('weigh_date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Weight (kg)',
                    'data' => $weightLogs->pluck('weight'),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $weightLogs->pluck('weigh_date')->map(fn ($date) => $date->format('M d')),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
