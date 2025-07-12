<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DebtResource\Pages;
use App\Models\Debt;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;

class DebtResource extends Resource
{
    protected static ?string $model = Debt::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $navigationLabel = 'الديون';
    protected static ?string $pluralModelLabel = 'الديون';
    protected static ?string $modelLabel = 'دين';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('subscriber_id')
                ->label('المشترك')
                ->relationship('subscriber', 'name')
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    // سحب مجموع الديون تلقائيًا
                    $debtTotal = Debt::where('subscriber_id', $state)->sum('amount');
                    $set('debt_value', $debtTotal);  // تعيين قيمة الديون
                }),

            Forms\Components\TextInput::make('amount')
                ->label('المبلغ')
                ->numeric()
                ->required(),

            Forms\Components\TextInput::make('reason')
                ->label('السبب')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subscriber.name')
                    ->label('المشترك')
                    ->searchable(),

                TextColumn::make('amount')
                    ->label('المبلغ')
                    ->suffix(' ل.س')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('reason')
                    ->label('السبب')
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime('Y-m-d H:i'),
            ])
            ->filters([])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDebts::route('/'),
            'edit' => Pages\EditDebt::route('/{record}/edit'),
        ];
    }
}
