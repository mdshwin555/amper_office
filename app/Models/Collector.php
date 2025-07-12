<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Collector extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'region',
        'generator_id',
    ];

    public function generator()
    {
        return $this->belongsTo(Generator::class);
    }

    public function subscribers()
    {
        return $this->hasMany(Subscriber::class);
    }
}
