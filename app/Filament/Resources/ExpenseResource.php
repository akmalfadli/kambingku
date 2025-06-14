<?php

namespace App\Filament\Resources;
use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use App\Helpers\CurrencyHelper;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;
    protected static ?string $modelLabel = 'Pengeluaran';
    protected static ?string $pluralModelLabel = 'Pengeluaran';
    protected static ?string $navigationIcon = 'heroicon-o-minus-circle';
    protected static ?string $navigationGroup = 'Manajemen Keuangan';
    protected static ?string $navigationLabel = 'Pengeluaran';
    protected static ?int $navigationSort = 4;


     public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'view' => Pages\ViewExpense::route('/{record}'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pengeluaran')
                    ->schema([
                        Forms\Components\Select::make('expense_type')
                            ->label('Jenis Pengeluaran')
                            ->options([
                                'feed' => 'Pakan',
                                'medical' => 'Medis/Kesehatan',
                                'equipment' => 'Peralatan',
                                'maintenance' => 'Perawatan Kandang',
                                'transportation' => 'Transportasi',
                                'labor' => 'Tenaga Kerja',
                                'utilities' => 'Utilitas (Listrik, Air)',
                                'other' => 'Lainnya',
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('expense_date')
                            ->label('Tanggal Pengeluaran')
                            ->required()
                            ->default(now()),
                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('0')
                            ->formatStateUsing(fn ($state) => $state ? CurrencyHelper::formatForInput($state) : '')
                            ->dehydrateStateUsing(fn ($state) => $state ? CurrencyHelper::parseRupiah($state) : 0),
                        Forms\Components\TextInput::make('supplier')
                            ->label('Pemasok/Vendor'),
                    ])->columns(2),

                Forms\Components\Section::make('Detail')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->required()
                            ->rows(3),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan Tambahan')
                            ->rows(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('expense_date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('expense_type')
                    ->label('Jenis')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'feed' => 'Pakan',
                        'medical' => 'Medis',
                        'equipment' => 'Peralatan',
                        'maintenance' => 'Perawatan',
                        'transportation' => 'Transportasi',
                        'labor' => 'Tenaga Kerja',
                        'utilities' => 'Utilitas',
                        'other' => 'Lainnya',
                        default => ucfirst($state),
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'feed' => 'success',
                        'medical' => 'danger',
                        'equipment' => 'info',
                        'maintenance' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->formatStateUsing(fn ($state) => CurrencyHelper::formatRupiah($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier')
                    ->label('Pemasok')
                    ->searchable()
                    ->placeholder('Tidak ada'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('expense_type')
                    ->label('Jenis Pengeluaran')
                    ->options([
                        'feed' => 'Pakan',
                        'medical' => 'Medis',
                        'equipment' => 'Peralatan',
                        'maintenance' => 'Perawatan',
                        'transportation' => 'Transportasi',
                        'labor' => 'Tenaga Kerja',
                        'utilities' => 'Utilitas',
                        'other' => 'Lainnya',
                    ]),
                Tables\Filters\Filter::make('expense_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($query) => $query->whereDate('expense_date', '>=', $data['from']))
                            ->when($data['until'], fn ($query) => $query->whereDate('expense_date', '<=', $data['until']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Lihat'),
                Tables\Actions\EditAction::make()->label('Edit'),
                Tables\Actions\DeleteAction::make()->label('Hapus'),
            ])
            ->defaultSort('expense_date', 'desc');
    }
}
