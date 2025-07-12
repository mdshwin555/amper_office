<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CollectorResource\Pages;
use App\Models\Collector;
use App\Models\Generator;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class CollectorResource extends Resource
{
    protected static ?string $model = Collector::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'الجباة';
    protected static ?string $pluralModelLabel = 'الجباة';
    protected static ?string $modelLabel = 'جابي';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->label('اسم الجابي')->required(),
            TextInput::make('phone')->label('رقم الهاتف')->required(),

            Select::make('region')
                ->label('المنطقة')
                ->options(
                    Generator::query()
                        ->select('region')
                        ->distinct()
                        ->pluck('region', 'region')
                        ->toArray()
                )
                ->searchable()
                ->required()
                ->reactive(),

            Select::make('generator_id')
                ->label('اسم المولدة - العنوان')
                ->options(function (callable $get) {
                    $region = $get('region');
                    return $region
                        ? Generator::where('region', $region)
                            ->get()
                            ->mapWithKeys(fn($g) => [$g->id => $g->name . ' - ' . $g->address])
                        : [];
                })
                ->required()
                ->searchable()
                ->reactive(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('اسم الجابي')->searchable(),
                TextColumn::make('phone')->label('رقم الهاتف'),
                TextColumn::make('region')->label('المنطقة'),
                TextColumn::make('generator.name')->label('المولدة'),

                TextColumn::make('subscriber_count')
                    ->label('عدد المشتركين')
                    ->getStateUsing(function ($record) {
                        $total = \App\Models\Collector::where('generator_id', $record->generator_id)
                            ->where('region', $record->region)
                            ->count();

                        $subs = \App\Models\Subscriber::where('region', $record->region)
                            ->where('generator_id', $record->generator_id)
                            ->count();

                        return floor($subs / max(1, $total));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCollectors::route('/'),
            'create' => Pages\CreateCollector::route('/create'),
            'edit' => Pages\EditCollector::route('/{record}/edit'),
        ];
    }
}
