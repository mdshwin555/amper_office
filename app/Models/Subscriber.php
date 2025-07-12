<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'box_number',
        'meter_number',
        'region',
        'generator_id',
        'status',
        'invoice_value',
        'debt_value',
    ];

    public function weeklyBills()
    {
        return $this->hasMany(WeeklyBill::class);
    }

    public function generator()
    {
        return $this->belongsTo(Generator::class);
    }

    public function latestWeeklyBill()
{
    return $this->hasOne(\App\Models\WeeklyBill::class)->latestOfMany();
}

public function debts()
{
    return $this->hasMany(\App\Models\Debt::class);
}

public function collector()
{
    return $this->belongsTo(Collector::class);
}


}
