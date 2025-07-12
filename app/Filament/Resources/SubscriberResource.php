<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriberResource\Pages;
use App\Models\Subscriber;
use App\Models\Generator;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class SubscriberResource extends Resource
{
    protected static ?string $model = Subscriber::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'المشتركين';
    protected static ?string $pluralModelLabel = 'المشتركين';
    protected static ?string $modelLabel = 'مشترك';

    // ✅ دالة تحويل الأرقام إلى إنكليزي
    public static function convertToEnglishNumbers($value): string
    {
        return strtr($value, ['٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->label('اسم المشترك')->required(),
            TextInput::make('phone')->label('رقم الهاتف')->required(),
            TextInput::make('box_number')->label('رقم العلبة')->required(),
            TextInput::make('meter_number')->label('رقم العداد')->required(),

            Select::make('region')
                ->label('المنطقة')
                ->options(
                    Generator::query()
                        ->select('region')
                        ->distinct()
                        ->pluck('region', 'region')
                        ->toArray()
                )
                ->required()
                ->reactive(),

            Select::make('generator_id')
                ->label('اسم المولدة - العنوان')
                ->options(function (callable $get) {
                    $region = $get('region');
                    return $region
                        ? Generator::where('region', $region)
                            ->get()
                            ->mapWithKeys(fn ($g) => [$g->id => $g->name . ' - ' . $g->address])
                        : [];
                })
                ->required()
                ->searchable()
                ->reactive(),

            Select::make('status')->label('الحالة')->options([
                'active' => 'مشترك',
                'inactive' => 'غير مشترك',
                'suspended' => 'مفصول',
            ])->hiddenOn('create')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => 
                $query->with(['generator', 'latestWeeklyBill', 'debts'])
            )
            ->columns([
                TextColumn::make('name')->label('اسم المشترك')->searchable(),
                TextColumn::make('phone')->label('رقم الهاتف'),
                TextColumn::make('box_number')->label('رقم العلبة'),
                TextColumn::make('meter_number')->label('رقم العداد'),
                TextColumn::make('region')->label('المنطقة'),
                TextColumn::make('generator.name')->label('اسم المولدة'),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn($record) => match ($record->status) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'suspended' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'active' => 'مشترك',
                        'inactive' => 'غير مشترك',
                        'suspended' => 'مفصول',
                        default => 'غير معروف',
                    }),

                TextColumn::make('latestWeeklyBill.amount_due')
                    ->label('قيمة آخر فاتورة')
                    ->getStateUsing(fn($record) => self::convertToEnglishNumbers($record->latestWeeklyBill->amount_due ?? 0)),

                TextColumn::make('debt_value')
                    ->label('إجمالي الديون')
                    ->getStateUsing(fn($record) => self::convertToEnglishNumbers($record->debts->sum('amount') ?? 0)),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscribers::route('/'),
            'create' => Pages\CreateSubscriber::route('/create'),
            'edit' => Pages\EditSubscriber::route('/{record}/edit'),
        ];
    }
}
