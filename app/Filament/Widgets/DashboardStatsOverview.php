<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Subscriber;
use App\Models\Debt;
use App\Models\WeeklyBill;

class DashboardStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            // الإحصائيات المتعلقة بالمشتركين
            Stat::make('عدد المشتركين', Subscriber::count())
                ->description('كل المشتركين المسجلين')
                ->descriptionIcon('heroicon-o-users'),

            // إجمالي الديون
            Stat::make('إجمالي الديون', number_format(Debt::sum('amount')) . ' ل.س')
                ->description('الديون غير المدفوعة')
                ->descriptionIcon('heroicon-o-exclamation-circle')
                ->color('danger'),

            // إجمالي الفواتير
            Stat::make('إجمالي الفواتير', number_format(WeeklyBill::sum('amount_due')) . ' ل.س')
                ->description('المبالغ الكليّة للفواتير')
                ->descriptionIcon('heroicon-o-currency-dollar'),

            // إجمالي المدفوع
            Stat::make('إجمالي المدفوع', number_format(WeeklyBill::sum('paid')) . ' ل.س')
                ->description('المبالغ التي تم دفعها')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}
