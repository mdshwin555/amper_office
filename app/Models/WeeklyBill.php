<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WeeklyBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscriber_id',
        'week_start',
        'week_end',
        'old_reading',
        'new_reading',
        'consumption',
        'price_per_kwh',
        'amount_due',
        'paid',
    ];

protected static function booted()
{
    static::created(function ($bill) {
        $debtAmount = $bill->amount_due - $bill->paid;

        if ($debtAmount > 0) {
            \App\Models\Debt::create([
                'subscriber_id' => $bill->subscriber_id,
                'amount' => $debtAmount,
                'reason' => 'دفعة ناقصة من فاتورة أسبوعية رقم ' . $bill->id,
            ]);
        }
    });

    static::updated(function ($bill) {
        $debtReason = 'دفعة ناقصة من فاتورة أسبوعية رقم ' . $bill->id;
        $existingDebt = \App\Models\Debt::where('subscriber_id', $bill->subscriber_id)
            ->where('reason', $debtReason)
            ->first();

        $newDebtAmount = $bill->amount_due - $bill->paid;

        if ($newDebtAmount <= 0 && $existingDebt) {
            $existingDebt->delete();
        } elseif ($newDebtAmount > 0) {
            if ($existingDebt) {
                $existingDebt->update(['amount' => $newDebtAmount]);
            } else {
                \App\Models\Debt::create([
                    'subscriber_id' => $bill->subscriber_id,
                    'amount' => $newDebtAmount,
                    'reason' => $debtReason,
                ]);
            }
        }
    });
}



    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }
}
