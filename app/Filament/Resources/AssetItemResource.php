<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetItemResource\Pages;
use App\Filament\Resources\AssetItemResource\RelationManagers;
use App\Models\AssetItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssetItemResource extends Resource
{
    protected static ?string $navigationGroup = 'Asset Management';
    protected static ?string $model = AssetItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('asset_category_id')
                    ->relationship('category', 'name')
                    ->label('Category')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('asset_location_id')
                    ->relationship('location', 'name')
                    ->label('Location')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('code'),
                Forms\Components\TextInput::make('serial_number'),
                Forms\Components\TextInput::make('purchase_price')
                    ->prefix('Rp')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric(),
                Forms\Components\DatePicker::make('purchase_date'),
                Forms\Components\Select::make('condition')
                    ->options([
                        'new' => 'New',
                        'good' => 'Good',
                        'poor' => 'Poor',
                    ])
                    ->default('good')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'in_use' => 'In Use (Sedang Digunakan)',
                        'in_stock' => 'In Stock / Storage (Tersimpan di Gudang)',
                        'in_repair' => 'In Repair / Maintenance (Dalam Perbaikan)',
                        'pending' => 'Pending / Provisioning (Dalam Persiapan)',
                        'lost_stolen' => 'Lost / Stolen (Hilang atau Dicuri)',
                        'retired_disposed' => 'Retired / Disposed (Pensiun / Dibuang)',
                        'expired' => 'Expired (Kadaluarsa)',
                    ])
                    ->default('in_use')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('location.name')
                    ->label('Location')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('serial_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('purchase_price')
                    ->prefix('Rp ')
                    ->numeric(thousandsSeparator: ',')
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchase_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('condition')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'new' => 'New',
                        'good' => 'Good',
                        'poor' => 'Poor',
                        default => ucfirst($state ?? '-'),
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'new' => 'success',
                        'good' => 'info',
                        'poor' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'in_use' => 'In Use',
                        'in_stock' => 'In Stock / Storage',
                        'in_repair' => 'In Repair / Maintenance',
                        'pending' => 'Pending / Provisioning',
                        'lost_stolen' => 'Lost / Stolen',
                        'retired_disposed' => 'Retired / Disposed',
                        'expired' => 'Expired',
                        default => ucfirst(str_replace('_', ' ', $state ?? '-')),
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'in_use' => 'success',
                        'in_stock' => 'info',
                        'in_repair' => 'warning',
                        'pending' => 'gray',
                        'lost_stolen' => 'danger',
                        'retired_disposed' => 'danger',
                        'expired' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'in_use' => 'In Use (Sedang Digunakan)',
                        'in_stock' => 'In Stock / Storage (Tersimpan di Gudang)',
                        'in_repair' => 'In Repair / Maintenance (Dalam Perbaikan)',
                        'pending' => 'Pending / Provisioning (Dalam Persiapan)',
                        'lost_stolen' => 'Lost / Stolen (Hilang atau Dicuri)',
                        'retired_disposed' => 'Retired / Disposed (Pensiun / Dibuang)',
                        'expired' => 'Expired (Kadaluarsa)',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssetItems::route('/'),
            'create' => Pages\CreateAssetItem::route('/create'),
            'edit' => Pages\EditAssetItem::route('/{record}/edit'),
        ];
    }
}

