<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GeneratorResource\Pages;
use App\Models\Generator;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;



class GeneratorResource extends Resource
{
    protected static ?string $model = Generator::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';
    protected static ?string $navigationLabel = 'المولدات';
    protected static ?string $pluralModelLabel = 'المولدات';
    protected static ?string $modelLabel = 'مولدة';



public static function form(Form $form): Form
{
    $regions = Generator::query()
        ->select('region')
        ->distinct()
        ->pluck('region')
        ->toArray();

    return $form
        ->schema([
            TextInput::make('region')
                ->label('المنطقة')
                ->datalist($regions) // يعرض الاقتراحات فقط، لكن ما يقيّد المستخدم
                ->required(),

            TextInput::make('address')
                ->label('العنوان')
                ->required()
                ->maxLength(255),

            TextInput::make('name')
                ->label('اسم المولدة')
                ->required()
                ->maxLength(255),
        ]);
}



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('اسم المولدة')->searchable(),
                TextColumn::make('region')->label('المنطقة')->searchable(),
                TextColumn::make('address')->label('العنوان')->searchable(),
            ])
            ->filters([])
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGenerators::route('/'),
            'create' => Pages\CreateGenerator::route('/create'),
            'edit' => Pages\EditGenerator::route('/{record}/edit'),
        ];
    }
}