<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    protected $fillable = [
        'subscriber_id',
        'amount',
        'reason',
    ];

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }
}
