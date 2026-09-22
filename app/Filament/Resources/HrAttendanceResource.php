<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HrAttendanceResource\Pages;
use App\Filament\Resources\HrAttendanceResource\RelationManagers;
use App\Models\HrAttendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HrAttendanceResource extends Resource
{
    protected static ?string $navigationGroup = 'HR & Attendance';
    protected static ?string $model = HrAttendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('hr_employee_id')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\DateTimePicker::make('check_in_time'),
                Forms\Components\DateTimePicker::make('check_out_time'),
                Forms\Components\TextInput::make('check_in_latitude')
                    ->numeric(),
                Forms\Components\TextInput::make('check_in_longitude')
                    ->numeric(),
                Forms\Components\TextInput::make('check_out_latitude')
                    ->numeric(),
                Forms\Components\TextInput::make('check_out_longitude')
                    ->numeric(),
                Forms\Components\TextInput::make('check_in_photo'),
                Forms\Components\TextInput::make('check_out_photo'),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hr_employee_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_in_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_out_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_in_latitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_in_longitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_out_latitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_out_longitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_in_photo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('check_out_photo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
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
                //
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
            'index' => Pages\ListHrAttendances::route('/'),
            'create' => Pages\CreateHrAttendance::route('/create'),
            'edit' => Pages\EditHrAttendance::route('/{record}/edit'),
        ];
    }
}

