<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticker extends Model
{
    public $timestamps = false;
    protected $fillable = ['ticker'];

    public function snapshots()
    {
        return $this->hasMany(Snapshot::class);
    }

    public function sentimentScores()
    {
        return $this->hasMany(SentimentScore::class);
    }
}
