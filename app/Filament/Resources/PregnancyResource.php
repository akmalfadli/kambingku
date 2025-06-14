<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PregnancyResource\Pages;
use App\Models\Pregnancy;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PregnancyResource extends Resource
{
    protected static ?string $model = Pregnancy::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationGroup = 'Manajemen Breeding';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pregnancy Details')
                    ->schema([
                        Forms\Components\Select::make('female_goat_id')
                            ->relationship('femaleGoat', 'tag_number')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('mating_record_id')
                            ->relationship('matingRecord', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "Mating #{$record->id} - {$record->mating_date->format('Y-m-d')}")
                            ->searchable()
                            ->required(),
                        Forms\Components\DatePicker::make('start_date')
                            ->required()
                            ->native(false),
                        Forms\Components\DatePicker::make('expected_delivery_date')
                            ->required()
                            ->native(false),
                        Forms\Components\DatePicker::make('actual_delivery_date')
                            ->native(false),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pregnant' => 'Pregnant',
                                'delivered' => 'Delivered',
                                'failed' => 'Failed',
                            ])
                            ->default('pregnant')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Health Notes')
                    ->schema([
                        Forms\Components\Textarea::make('health_notes')
                            ->rows(4),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('femaleGoat.tag_number')
                    ->label('Female Goat')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expected_delivery_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('actual_delivery_date')
                    ->date()
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pregnant' => 'warning',
                        'delivered' => 'success',
                        'failed' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('days_remaining')
                    ->label('Days Remaining')
                    ->state(function (Pregnancy $record): string {
                        if ($record->status !== 'pregnant') return '—';
                        $days = now()->diffInDays($record->expected_delivery_date, false);
                        return $days > 0 ? "{$days} days" : 'Overdue';
                    })
                    ->color(function (Pregnancy $record): string {
                        if ($record->status !== 'pregnant') return 'gray';
                        $days = now()->diffInDays($record->expected_delivery_date, false);
                        return $days <= 7 ? 'danger' : ($days <= 30 ? 'warning' : 'success');
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pregnant' => 'Pregnant',
                        'delivered' => 'Delivered',
                        'failed' => 'Failed',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('record_delivery')
                    ->label('Record Delivery')
                    ->icon('heroicon-o-gift')
                    ->color('success')
                    ->visible(fn (Pregnancy $record): bool => $record->status === 'pregnant')
                    ->form([
                        Forms\Components\DatePicker::make('delivery_date')
                            ->default(now())
                            ->required(),
                        Forms\Components\TextInput::make('number_of_kids')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                        Forms\Components\Repeater::make('kids_details')
                            ->schema([
                                Forms\Components\Select::make('gender')
                                    ->options(['male' => 'Male', 'female' => 'Female'])
                                    ->required(),
                                Forms\Components\TextInput::make('tag_id')
                                    ->required()
                                    ->unique('goats', 'tag_number'),
                                Forms\Components\Select::make('survival_status')
                                    ->options(['alive' => 'Alive', 'dead' => 'Dead'])
                                    ->default('alive')
                                    ->required(),
                            ])
                            ->columns(3)
                            ->addActionLabel('Add Kid')
                            ->minItems(1),
                        Forms\Components\Textarea::make('delivery_notes'),
                    ])
                    ->action(function (Pregnancy $record, array $data): void {
                        $record->kiddingRecords()->create([
                            'mother_goat_id' => $record->female_goat_id,
                            'delivery_date' => $data['delivery_date'],
                            'number_of_kids' => $data['number_of_kids'],
                            'kids_details' => $data['kids_details'],
                            'delivery_notes' => $data['delivery_notes'] ?? null,
                        ]);

                        $record->update([
                            'actual_delivery_date' => $data['delivery_date'],
                            'status' => 'delivered',
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('expected_delivery_date', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPregnancies::route('/'),
            'create' => Pages\CreatePregnancy::route('/create'),
            'view' => Pages\ViewPregnancy::route('/{record}'),
            'edit' => Pages\EditPregnancy::route('/{record}/edit'),
        ];
    }
}
