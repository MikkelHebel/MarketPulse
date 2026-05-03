<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecentSearch extends Model
{
    protected $fillable = ['ticker_id'];

    public function ticker()
    {
        return $this->belongsTo(Ticker::class);
    }
}
