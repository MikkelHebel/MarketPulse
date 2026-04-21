<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticker extends Model
{
    protected $timestamps = false;
    protected $fillable = ['ticker'];

    public function snapshots()
    {
        return $this->hasMany(Snapshot::class);
    }

    public function sentimentScore()
    {
        return $this->hasMany(SentimentScore::class);
    }
}
