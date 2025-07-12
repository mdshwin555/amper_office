<?php

namespace App\Filament\Resources\WeeklyBillResource\Pages;

use App\Filament\Resources\WeeklyBillResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWeeklyBill extends EditRecord
{
    protected static string $resource = WeeklyBillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
