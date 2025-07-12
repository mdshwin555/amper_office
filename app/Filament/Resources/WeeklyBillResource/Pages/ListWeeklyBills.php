<?php

namespace App\Filament\Resources\WeeklyBillResource\Pages;

use App\Filament\Resources\WeeklyBillResource;
use App\Models\Generator;
use App\Models\Collector;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListWeeklyBills extends ListRecords
{
    protected static string $resource = WeeklyBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // زر إضافة فاتورة
            Actions\CreateAction::make()
                ->label('➕ إضافة فاتورة'),

            // زر طباعة الفواتير
            Action::make('printBills')
                ->label('🖨️ طباعة فواتير')
                ->color('gray')
                ->form([
                    Select::make('generator_id')
                        ->label('المولدة (اختياري)')
                        ->options(Generator::pluck('name', 'id'))
                        ->searchable()
                        ->placeholder('اختر مولدة أو اتركه فارغ'),

                    Select::make('collector_id')
                        ->label('الجابي (اختياري)')
                        ->options(Collector::pluck('name', 'id'))
                        ->searchable()
                        ->placeholder('اختر جابي أو اتركه فارغ'),

                    TextInput::make('count')
                        ->label('عدد الفواتير')
                        ->numeric()
                        ->default(10)
                        ->required(),
                ])
                ->action(function (array $data) {
                    if (empty($data['generator_id']) && empty($data['collector_id'])) {
                        // لو ما اختار ولا واحد، رجّع رسالة خطأ
                        notify()->error('يرجى اختيار الجابي أو المولدة على الأقل');
                        return;
                    }

                    return redirect('/print-bills?' . http_build_query($data));
                }),
        ];
    }
}
