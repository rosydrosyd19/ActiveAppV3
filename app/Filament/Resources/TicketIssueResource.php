<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketIssueResource\Pages;
use App\Filament\Resources\TicketIssueResource\RelationManagers;
use App\Models\TicketIssue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketIssueResource extends Resource
{
    protected static ?string $navigationGroup = 'Ticketing';
    protected static ?string $model = TicketIssue::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('User')
                    ->default(fn () => auth()->id())
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                Forms\Components\Select::make('ticket_category_id')
                    ->relationship('category', 'name')
                    ->label('Ticket Category')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('assignees')
                    ->relationship('assignees', 'name', fn (Builder $query) => $query->role(['teknisi', 'it']))
                    ->multiple()
                    ->label('Assigned To')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('title')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ])
                    ->default('medium')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'resolved' => 'Resolved',
                        'closed' => 'Closed',
                    ])
                    ->default('open')
                    ->required(),
                Forms\Components\DateTimePicker::make('resolved_at')
                    ->hiddenOn('create'),
                Forms\Components\FileUpload::make('photo')
                    ->image()
                    ->directory('ticket-photos')
                    ->nullable(),
                Forms\Components\Section::make('Location & Asset')
                    ->description('Pilih lokasi terlebih dahulu untuk memfilter aset yang tersedia.')
                    ->schema([
                        Forms\Components\Select::make('asset_location_id')
                            ->relationship('assetLocation', 'name')
                            ->label('Location')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (\Filament\Forms\Set $set) => $set('asset_item_id', null)),
                        Forms\Components\Select::make('asset_item_id')
                            ->relationship('assetItem', 'name', fn (Builder $query, \Filament\Forms\Get $get) => 
                                $query->where('asset_location_id', $get('asset_location_id'))
                            )
                            ->label('Asset')
                            ->searchable()
                            ->preload()
                            ->disabled(fn (\Filament\Forms\Get $get): bool => ! filled($get('asset_location_id'))),
                    ])
                    ->columns(2),
                Forms\Components\Textarea::make('problem')
                    ->visibleOn(['edit', 'view'])
                    ->nullable(),
                Forms\Components\Textarea::make('solution')
                    ->visibleOn(['edit', 'view'])
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Ticket Category')
                    ->badge()
                    ->color(fn (\Illuminate\Database\Eloquent\Model $record): string => $record->category?->color ?? 'gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('assetLocation.name')
                    ->label('Location')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('assignees.name')
                    ->label('Assigned To')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('priority')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'resolved' => 'Resolved',
                        'closed' => 'Closed',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'danger',
                        'in_progress' => 'warning',
                        'resolved' => 'success',
                        'closed' => 'gray',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('resolved_at')
                    ->dateTime()
                    ->sortable(),
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
                Tables\Filters\SelectFilter::make('asset_location_id')
                    ->multiple()
                    ->relationship('assetLocation', 'name')
                    ->label('Location')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('user_id')
                    ->multiple()
                    ->relationship('user', 'name')
                    ->label('User')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('ticket_category_id')
                    ->multiple()
                    ->relationship('category', 'name')
                    ->label('Ticket Category')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('assignees')
                    ->multiple()
                    ->relationship('assignees', 'name')
                    ->label('Assigned To')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('priority')
                    ->multiple()
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ])
                    ->label('Priority'),
                Tables\Filters\SelectFilter::make('status')
                    ->multiple()
                    ->options([
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'resolved' => 'Resolved',
                        'closed' => 'Closed',
                    ])
                    ->label('Status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListTicketIssues::route('/'),
            'create' => Pages\CreateTicketIssue::route('/create'),
            'view' => Pages\ViewTicketIssue::route('/{record}'),
            'edit' => Pages\EditTicketIssue::route('/{record}/edit'),
        ];
    }
}

