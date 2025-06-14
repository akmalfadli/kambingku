<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Models\Sale;
use App\Models\Goat;
use App\Helpers\CurrencyHelper;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;
    protected static ?string $modelLabel = 'Penjualan';
    protected static ?string $pluralModelLabel = 'Penjualan';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Manajemen Keuangan';
    protected static ?string $navigationLabel = 'Penjualan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Penjualan')
                    ->schema([
                        Forms\Components\Select::make('goat_id')
                            ->label('Kambing')
                            ->relationship('goat', 'tag_number', fn ($query) => $query->where('status', 'active'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                if ($state) {
                                    $goat = Goat::find($state);
                                    if ($goat) {
                                        // Auto-fill cost price from purchase_price for fattening goats
                                        if ($goat->purchase_price && $goat->type === 'fattening') {
                                            $set('cost_price', $goat->purchase_price);
                                        }
                                        // Auto-fill weight from latest weight
                                        if ($goat->latest_weight) {
                                            $set('weight_at_sale', $goat->latest_weight);
                                        }
                                        // Update profit calculation
                                        self::updateProfit($set, $get);
                                    }
                                }
                            })
                            ->getOptionLabelUsing(function ($value) {
                                $goat = Goat::find($value);
                                return $goat ? "{$goat->tag_number} - {$goat->name} ({$goat->breed})" : $value;
                            }),
                        Forms\Components\DatePicker::make('sale_date')
                            ->label('Tanggal Penjualan')
                            ->required()
                            ->default(now()),
                        Forms\Components\TextInput::make('buyer_name')
                            ->label('Nama Pembeli')
                            ->required(),
                        Forms\Components\TextInput::make('buyer_contact')
                            ->label('Kontak Pembeli')
                            ->tel(),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Keuangan')
                    ->schema([
                        Forms\Components\TextInput::make('sale_price')
                            ->label('Harga Jual')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('0')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                self::updateProfit($set, $get);
                            }),
                        Forms\Components\TextInput::make('cost_price')
                            ->label('Harga Pokok/Beli')
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('0')
                            ->helperText('Akan diisi otomatis dari harga beli kambing penggemukan')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                self::updateProfit($set, $get);
                            }),
                        Forms\Components\TextInput::make('profit')
                            ->label('Keuntungan')
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Akan dihitung otomatis'),
                        Forms\Components\TextInput::make('weight_at_sale')
                            ->label('Berat Saat Dijual')
                            ->numeric()
                            ->suffix('kg')
                            ->placeholder('Akan diisi dari berat terakhir'),
                    ])->columns(2),

                Forms\Components\Section::make('Informasi Kambing Terpilih')
                    ->schema([
                        Forms\Components\Placeholder::make('goat_info')
                            ->label('Info Kambing')
                            ->content(function (Forms\Get $get): string {
                                $goatId = $get('goat_id');
                                if (!$goatId) {
                                    return 'Pilih kambing terlebih dahulu';
                                }

                                $goat = Goat::find($goatId);
                                if (!$goat) {
                                    return 'Kambing tidak ditemukan';
                                }

                                $info = [
                                    "Tag: {$goat->tag_number}",
                                    "Nama: " . ($goat->name ?: 'Tidak ada'),
                                    "Ras: {$goat->breed}",
                                    "Tipe: " . ($goat->type === 'fattening' ? 'Penggemukan' : 'Breeding'),
                                ];

                                if ($goat->type === 'fattening' && $goat->purchase_price) {
                                    $info[] = "Harga Beli: " . CurrencyHelper::formatRupiah($goat->purchase_price);
                                }

                                if ($goat->current_weight) {
                                    $info[] = "Berat Terakhir: {$goat->current_weight} kg";
                                }

                                if ($goat->origin) {
                                    $info[] = "Asal: {$goat->origin}";
                                }

                                return implode(' | ', $info);
                            }),
                    ])
                    ->visible(fn (Forms\Get $get): bool => (bool) $get('goat_id')),

                Forms\Components\Section::make('Catatan')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->placeholder('Catatan tambahan mengenai penjualan'),
                    ]),
            ])
            ->live();
    }


    // Helper method to update profit calculation
    public static function updateProfit(Forms\Set $set, Forms\Get $get): void
    {
        $salePrice = (float) ($get('sale_price') ?? 0);
        $costPrice = (float) ($get('cost_price') ?? 0);
        $profit = $salePrice - $costPrice;

        $set('profit', number_format($profit, 0, ',', '.'));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sale_date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('goat.tag_number')
                    ->label('Kambing')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('goat.name')
                    ->label('Nama Kambing')
                    ->searchable()
                    ->placeholder('Tidak ada nama')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('goat.type')
                    ->label('Tipe')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'breeding' => 'Breeding',
                        'fattening' => 'Penggemukan',
                        default => ucfirst($state),
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'breeding' => 'success',
                        'fattening' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('buyer_name')
                    ->label('Pembeli')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sale_price')
                    ->label('Harga Jual')
                    ->formatStateUsing(fn ($state) => CurrencyHelper::formatRupiah($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost_price')
                    ->label('Harga Pokok')
                    ->formatStateUsing(fn ($state) => CurrencyHelper::formatRupiah($state ?? 0))
                    ->sortable(),
                Tables\Columns\TextColumn::make('profit')
                    ->label('Keuntungan')
                    ->state(function (Sale $record): int {
                        return $record->sale_price - ($record->cost_price ?? 0);
                    })
                    ->formatStateUsing(fn ($state) => CurrencyHelper::formatRupiah($state))
                    ->color(fn ($state) => $state >= 0 ? 'success' : 'danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('profit_percentage')
                    ->label('% Keuntungan')
                    ->state(function (Sale $record): string {
                        if (!$record->cost_price || $record->cost_price == 0) {
                            return 'N/A';
                        }
                        $profit = $record->sale_price - $record->cost_price;
                        $percentage = ($profit / $record->cost_price) * 100;
                        return number_format($percentage, 1) . '%';
                    })
                    ->color(fn ($state) => $state !== 'N/A' && floatval($state) >= 0 ? 'success' : 'danger')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('weight_at_sale')
                    ->label('Berat')
                    ->suffix(' kg')
                    ->placeholder('Tidak dicatat')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('sale_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($query) => $query->whereDate('sale_date', '>=', $data['from']))
                            ->when($data['until'], fn ($query) => $query->whereDate('sale_date', '<=', $data['until']));
                    }),
                Tables\Filters\SelectFilter::make('goat_type')
                    ->label('Tipe Kambing')
                    ->relationship('goat', 'type')
                    ->options([
                        'breeding' => 'Breeding',
                        'fattening' => 'Penggemukan',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Lihat'),
                Tables\Actions\EditAction::make()->label('Edit'),
                Tables\Actions\DeleteAction::make()->label('Hapus')->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Hapus Terpilih'),
                ]),
            ])
            ->defaultSort('sale_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'view' => Pages\ViewSale::route('/{record}'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }
}
