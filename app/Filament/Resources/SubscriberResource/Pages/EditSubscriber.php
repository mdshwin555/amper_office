<?php

namespace App\Filament\Resources\SubscriberResource\Pages;

use App\Filament\Resources\SubscriberResource;
use App\Models\WeeklyBill;
use App\Models\Debt;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class EditSubscriber extends EditRecord
{
    protected static string $resource = SubscriberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('اسم المشترك')
                ->required(),

            TextInput::make('phone')
                ->label('رقم الهاتف')
                ->required(),

            TextInput::make('box_number')
                ->label('رقم العلبة')
                ->required(),

            TextInput::make('meter_number')
                ->label('رقم العداد')
                ->required(),

            // الحقل الخاص بالحالة باستخدام DropDown
            Select::make('status')
                ->label('الحالة')
                ->options([
                    'active' => 'مشترك',
                    'inactive' => 'غير مشترك',
                    'suspended' => 'مفصول مؤقت',
                ])
                ->default('active') // تعيين القيمة الافتراضية
                ->required(),

            // سحب قيمة الفاتورة للمشترك تلقائيًا
            TextInput::make('invoice_value')
                ->label('قيمة الفاتورة')
                ->default(function ($state, callable $set) {
                    // التأكد من أن المعرف الخاص بالمشترك موجود
                    if (isset($state->id)) {
                        $invoiceValue = WeeklyBill::where('subscriber_id', $state->id)
                            ->orderByDesc('created_at')
                            ->value('amount_due');
                        return $invoiceValue ?? 0; // إذا لم يكن هناك قيمة، يعطى 0
                    }
                    return 0;
                })
                ->readOnly(), // لا يمكن تعديل القيمة من قبل المستخدم

            // سحب قيمة الديون للمشترك تلقائيًا
            TextInput::make('debt_value')
                ->label('قيمة الديون')
                ->default(function ($state, callable $set) {
                    // التأكد من أن المعرف الخاص بالمشترك موجود
                    if (isset($state->id)) {
                        $debtValue = Debt::where('subscriber_id', $state->id)->sum('amount');
                        return $debtValue ?? 0; // إذا لم يكن هناك دين، يعطى 0
                    }
                    return 0;
                })
                ->readOnly(), // لا يمكن تعديل القيمة من قبل المستخدم
        ];
    }
}
