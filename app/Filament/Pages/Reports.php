<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Goat;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\FeedingLog;
use App\Models\HealthRecord;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class Reports extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Laporan';
    protected static string $view = 'filament.pages.reports';
    protected static ?int $navigationSort = 5;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
            'report_type' => 'financial',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('report_type')
                    ->options([
                        'financial' => 'Financial Summary',
                        'livestock' => 'Livestock Summary',
                        'breeding' => 'Breeding Performance',
                        'health' => 'Health & Medicine',
                        'feeding' => 'Feeding Analysis',
                    ])
                    ->required(),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date')
                    ->required(),
            ])
            ->statePath('data')
            ->columns(3);
    }

    public function generateReport(): void
    {
        $data = $this->form->getState();

        // Generate report logic here based on report_type
        $this->dispatch('report-generated', $data);
    }

    public function getReportData(): array
    {
        $startDate = $this->data['start_date'] ?? now()->startOfMonth();
        $endDate = $this->data['end_date'] ?? now()->endOfMonth();
        $reportType = $this->data['report_type'] ?? 'financial';

        switch ($reportType) {
            case 'financial':
                return [
                    'total_revenue' => Sale::whereBetween('sale_date', [$startDate, $endDate])->sum('sale_price'),
                    'total_profit' => Sale::whereBetween('sale_date', [$startDate, $endDate])->sum('profit'),
                    'total_expenses' => Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount'),
                    'feed_costs' => FeedingLog::whereBetween('feeding_date', [$startDate, $endDate])->sum('cost'),
                    'health_costs' => HealthRecord::whereBetween('record_date', [$startDate, $endDate])->sum('cost'),
                ];

            case 'livestock':
                return [
                    'total_goats' => Goat::count(),
                    'active_goats' => Goat::where('status', 'active')->count(),
                    'breeding_goats' => Goat::where('type', 'breeding')->where('status', 'active')->count(),
                    'fattening_goats' => Goat::where('type', 'fattening')->where('status', 'active')->count(),
                    'sold_goats' => Goat::where('status', 'sold')->count(),
                ];

            default:
                return [];
        }
    }
}
