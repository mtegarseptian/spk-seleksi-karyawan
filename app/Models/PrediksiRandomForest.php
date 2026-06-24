<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrediksiRandomForest extends Model
{
    protected $table = 'prediksi_random_forests';
    protected $guarded = [];

    public function kandidat()
    {
        return $this->belongsTo(Kandidat::class);
    }
}