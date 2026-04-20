<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    //
    protected $fillable = [
        'subindi_id',
        'name',
        'status'
    ];

    public function subindi()
    {
        return $this->belongsTo(Subindi::class);
    }
}
