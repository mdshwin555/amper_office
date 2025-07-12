<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WeeklyBillResource\Pages;
use App\Models\WeeklyBill;
use App\Models\Subscriber;
use App\Models\Debt;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Carbon;

class WeeklyBillResource extends Resource
{
    protected static ?string $model = WeeklyBill::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'الفواتير الأسبوعية';
    protected static ?string $pluralModelLabel = 'الفواتير الأسبوعية';
    protected static ?string $modelLabel = 'فاتورة';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('subscriber_id')
                ->label('اسم المشترك')
                ->relationship('subscriber', 'name')
                ->searchable()
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    $lastReading = WeeklyBill::where('subscriber_id', $state)
                        ->orderByDesc('created_at')
                        ->value('new_reading') ?? 0;
                    $set('old_reading', $lastReading);

                    $debtTotal = Debt::where('subscriber_id', $state)->sum('amount');
                    $price = 10000;
                    $set('price_per_kwh', $price);
                    $set('amount_due', $debtTotal);
                }),

            TextInput::make('old_reading')
                ->label('القراءة القديمة')
                ->numeric()
                ->required()
                ->default(0)
                ->readOnly(),

            TextInput::make('new_reading')
                ->label('القراءة الجديدة')
                ->numeric()
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, $get) {
                    $old = floatval($get('old_reading'));
                    $price = floatval($get('price_per_kwh'));
                    $consumption = max(0, floatval($state) - $old);
                    $set('consumption', $consumption);
                    $currentAmount = floatval($get('amount_due'));
                    $set('amount_due', $currentAmount + ($consumption * $price));
                }),

            TextInput::make('price_per_kwh')
                ->label('سعر الكيلو')
                ->numeric()
                ->required()
                ->default(10000)
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, $get) {
                    $consumption = floatval($get('consumption'));
                    $debts = Debt::where('subscriber_id', $get('subscriber_id'))->sum('amount');
                    $set('amount_due', ($consumption * floatval($state)) + $debts);
                }),

            TextInput::make('consumption')
                ->label('كمية الاستهلاك')
                ->numeric()
                ->readOnly(),

            TextInput::make('amount_due')
                ->label('المبلغ المستحق')
                ->numeric()
                ->readOnly(),

            TextInput::make('paid')
                ->label('المبلغ المدفوع')
                ->numeric()
                ->default(0),

            DatePicker::make('week_start')
                ->label('الدورة')
                ->required()
                ->default(now())
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    $startDate = Carbon::parse($state);
                    $set('week_end', $startDate->copy()->addDays(6));
                }),

            TextInput::make('week_end')
                ->hidden(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subscriber.name')->label('اسم المشترك')->searchable(),
                TextColumn::make('subscriber.generator')->label('المولدة')->getStateUsing(function ($record) {
                    $region = $record->subscriber->region;
                    $genName = $record->subscriber->generator->name ?? '';
                    $gensInRegion = $record->subscriber->region
                        ? \App\Models\Generator::where('region', $region)->count()
                        : 0;
                    return $gensInRegion > 1 ? "$region - $genName" : $region;
                }),
                TextColumn::make('old_reading')->label('القراءة السابقة'),
                TextColumn::make('new_reading')->label('القراءة الحالية'),
                TextColumn::make('consumption')->label('الاستهلاك'),
                TextColumn::make('price_per_kwh')->label('سعر الكيلو')->formatStateUsing(function ($state, $record) {
                    $discounted = $record->discount_per_kwh;
                    if ($discounted > 0) {
                        $newPrice = $state - $discounted;
                        return "<s>$state</s><br><span>$newPrice</span>";
                    }
                    return $state;
                })->html(),
                TextColumn::make('subscriber.debt_value')->label('الديون')->getStateUsing(fn($record) => Debt::where('subscriber_id', $record->subscriber_id)->sum('amount')),
                TextColumn::make('amount_due')->label('المبلغ الاجمالي')->suffix(' ل.س')->formatStateUsing(function ($state, $record) {
                    $discount = $record->discount_total;
                    if ($discount > 0) {
                        $newTotal = $state - $discount;
                        return "<s>$state</s><br><span>$newTotal</span> ل.س";
                    }
                    return "$state ل.س";
                })->html(),
                TextColumn::make('paid')->label('المدفوع')->suffix(' ل.س'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('addPayment')
                    ->label('إضافة دفعة')
                    ->form([
                        TextInput::make('payment')->label('قيمة الدفعة')->numeric()->required(),
                        TextInput::make('discount_per_kwh')->label('خصم على الكيلو')->numeric()->default(0),
                        TextInput::make('discount_total')->label('خصم على الفاتورة')->numeric()->default(0),
                    ])
                    ->action(function (array $data, WeeklyBill $record) {
                        $discountedPrice = $record->price_per_kwh - floatval($data['discount_per_kwh']);
                        $newAmount = $record->consumption * $discountedPrice - floatval($data['discount_total']);

                        $record->update([
                            'amount_due' => $newAmount,
                            'paid' => $record->paid + floatval($data['payment']),
                            'discount_per_kwh' => floatval($data['discount_per_kwh']),
                            'discount_total' => floatval($data['discount_total']),
                        ]);
                    })
                    ->modalHeading('إضافة دفعة وخصم')
                    ->modalSubmitActionLabel('حفظ')
                    ->modalCancelActionLabel('إلغاء'),
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
            'index' => Pages\ListWeeklyBills::route('/'),
            'create' => Pages\CreateWeeklyBill::route('/create'),
            'edit' => Pages\EditWeeklyBill::route('/{record}/edit'),
        ];
    }
}
