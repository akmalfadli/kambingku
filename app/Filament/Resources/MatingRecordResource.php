<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MatingRecordResource\Pages;
use App\Models\MatingRecord;
use App\Models\Goat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MatingRecordResource extends Resource
{
    protected static ?string $model = MatingRecord::class;
    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationGroup = 'Manajemen Breeding';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Mating Information')
                    ->schema([
                        Forms\Components\Select::make('male_goat_id')
                            ->label('Male Goat')
                            ->options(Goat::where('gender', 'male')->where('status', 'active')->pluck('tag_number', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('female_goat_id')
                            ->label('Female Goat')
                            ->options(Goat::where('gender', 'female')->where('status', 'active')->pluck('tag_number', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\DatePicker::make('mating_date')
                            ->required()
                            ->default(now())
                            ->native(false),
                        Forms\Components\DatePicker::make('expected_delivery_date')
                            ->label('Expected Delivery Date')
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(2),

                Forms\Components\Section::make('Outcome & Notes')
                    ->schema([
                        Forms\Components\Select::make('outcome')
                            ->options([
                                'pending' => 'Pending',
                                'successful' => 'Successful',
                                'failed' => 'Failed',
                            ])
                            ->default('pending')
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('maleGoat.tag_number')
                    ->label('Male Goat')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('femaleGoat.tag_number')
                    ->label('Female Goat')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mating_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expected_delivery_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('outcome')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'successful' => 'success',
                        'failed' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('outcome')
                    ->options([
                        'pending' => 'Pending',
                        'successful' => 'Successful',
                        'failed' => 'Failed',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('create_pregnancy')
                    ->label('Create Pregnancy')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->visible(fn (MatingRecord $record): bool => $record->outcome === 'successful' && !$record->pregnancy)
                    ->action(function (MatingRecord $record): void {
                        $record->pregnancy()->create([
                            'female_goat_id' => $record->female_goat_id,
                            'start_date' => $record->mating_date,
                            'expected_delivery_date' => $record->expected_delivery_date,
                            'status' => 'pregnant',
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMatingRecords::route('/'),
            'create' => Pages\CreateMatingRecord::route('/create'),
            'view' => Pages\ViewMatingRecord::route('/{record}'),
            'edit' => Pages\EditMatingRecord::route('/{record}/edit'),
        ];
    }
}
