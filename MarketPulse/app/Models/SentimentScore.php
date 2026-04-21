<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SentimentScore extends Model
{
    protected $timestamps = false;
    protected $fillable = ['score', 'ticker_id', 'timestamp'];

    public function ticker()
    {
        return $this->belongsTo(Ticker::class);
    }
}
