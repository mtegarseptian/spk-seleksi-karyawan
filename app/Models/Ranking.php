<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    protected $table = 'rankings';
    protected $guarded = [];

    public function kandidat()
    {
        return $this->belongsTo(Kandidat::class);
    }
}