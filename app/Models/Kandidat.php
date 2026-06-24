<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kandidat extends Model
{
    protected $table = 'kandidats';
    protected $guarded = [];

    public function prediksi()
    {
        return $this->hasOne(PrediksiRandomForest::class);
    }

    public function ranking()
    {
        return $this->hasOne(Ranking::class);
    }
}