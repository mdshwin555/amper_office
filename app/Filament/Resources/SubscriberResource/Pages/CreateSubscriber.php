<?php

namespace App\Filament\Resources\SubscriberResource\Pages;

use App\Filament\Resources\SubscriberResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class CreateSubscriber extends CreateRecord
{
    protected static string $resource = SubscriberResource::class;

    // هذا هو الكود المعدل: إزالة الحقول "قيمة الفاتورة" و "قيمة الديون"
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

            // إضافة الحقل "الحالة" كـ Dropdown مع القيمة الافتراضية "مشترك"
            Select::make('status')
                ->label('الحالة')
                ->options([
                    'active' => 'مشترك',
                    'inactive' => 'غير مشترك',
                    'suspended' => 'مفصول مؤقت',
                ])
                ->default('active')  // تعيين القيمة الافتراضية للمشتركين الجدد
                ->required(),
        ];
    }
}
