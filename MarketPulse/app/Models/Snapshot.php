<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Snapshot extends Model
{
    public $timestamps = false;
    protected $fillable = ['price', 'ticker_id', 'timestamp'];

    public function ticker()
    {
        return $this->belongsTo(Ticker::class);
    }
}
