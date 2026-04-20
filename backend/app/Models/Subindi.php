<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subindi extends Model
{
    //
    protected $fillable = [
        'indi_id',
        'name',
        'status'
    ];

    public function indi()
    {
        return $this->belongsTo(Indi::class);
    }

}
