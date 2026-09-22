<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApprovalRequestResource\Pages;
use App\Models\ApprovalRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ApprovalRequestResource extends Resource
{
    protected static ?string $navigationGroup = 'Requests & Approvals';
    protected static ?string $model = ApprovalRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Approval Requests';
    protected static ?string $pluralModelLabel = 'Approval Requests';
    protected static ?string $modelLabel = 'Approval Request';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Request Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Requester')
                            ->default(fn () => auth()->id())
                            ->required()
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('reference_number')
                            ->label('Reference Number')
                            ->default(fn () => 'REQ-' . date('Ymd-His'))
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->label('Request Title')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->default('pending')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Requested Items')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->label('Items')
                            ->schema([
                                Forms\Components\TextInput::make('item_name')
                                    ->label('Item Name')
                                    ->required(),
                                Forms\Components\TextInput::make('specification')
                                    ->label('Specification'),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('unit')
                                    ->label('Unit (Pcs, Box, etc)')
                                    ->required(),
                                Forms\Components\TextInput::make('estimated_price')
                                    ->label('Estimated Unit Price')
                                    ->numeric(),
                                Forms\Components\TextInput::make('total_price')
                                    ->label('Total Price')
                                    ->numeric(),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add New Item')
                            ->defaultItems(1)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('Ref. No')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Requester')
                    ->sortable(),
                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items Count'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApprovalRequests::route('/'),
            'create' => Pages\CreateApprovalRequest::route('/create'),
            'view' => Pages\ViewApprovalRequest::route('/{record}'),
            'edit' => Pages\EditApprovalRequest::route('/{record}/edit'),
        ];
    }
}

