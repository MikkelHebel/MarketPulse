<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserThreshold extends Model
{
    protected $fillable = [
        'user_id',
        'ticker_id',
        'hci_high',
        'hci_low',
    ];

    public function ticker()
    {
        return $this->belongsTo(Ticker::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
