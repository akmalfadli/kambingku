<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HealthRecordResource\Pages;
use App\Models\HealthRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HealthRecordResource extends Resource
{
    protected static ?string $model = HealthRecord::class;
    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationGroup = 'Operasional';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Health Record Information')
                    ->schema([
                        Forms\Components\Select::make('goat_id')
                            ->relationship('goat', 'tag_number')
                            ->searchable()
                            ->required(),
                        Forms\Components\DatePicker::make('record_date')
                            ->required()
                            ->default(now())
                            ->native(false),
                        Forms\Components\TextInput::make('diagnosis')
                            ->required(),
                        Forms\Components\Textarea::make('treatment')
                            ->required()
                            ->rows(4),
                    ])->columns(2),

                Forms\Components\Section::make('Medication & Cost')
                    ->schema([
                        Forms\Components\TextInput::make('medicine_given')
                            ->label('Medicine Given'),
                        Forms\Components\TextInput::make('cost')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                        Forms\Components\TextInput::make('vet_name')
                            ->label('Veterinarian Name'),
                        Forms\Components\DatePicker::make('next_checkup_date')
                            ->label('Next Checkup Date')
                            ->native(false),
                    ])->columns(2),

                Forms\Components\Section::make('Additional Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('goat.tag_number')
                    ->label('Goat')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('record_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('diagnosis')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('treatment')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->treatment),
                Tables\Columns\TextColumn::make('medicine_given')
                    ->label('Medicine')
                    ->limit(20)
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('cost')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('vet_name')
                    ->label('Vet')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('next_checkup_date')
                    ->label('Next Checkup')
                    ->date()
                    ->placeholder('—')
                    ->color(fn ($record) => $record->next_checkup_date && $record->next_checkup_date->isPast() ? 'danger' : null),
            ])
            ->filters([
                Tables\Filters\Filter::make('upcoming_checkups')
                    ->label('Upcoming Checkups')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('next_checkup_date')->where('next_checkup_date', '>=', now())),
                Tables\Filters\Filter::make('overdue_checkups')
                    ->label('Overdue Checkups')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('next_checkup_date')->where('next_checkup_date', '<', now())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('record_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHealthRecords::route('/'),
            'create' => Pages\CreateHealthRecord::route('/create'),
            'view' => Pages\ViewHealthRecord::route('/{record}'),
            'edit' => Pages\EditHealthRecord::route('/{record}/edit'),
        ];
    }
}
