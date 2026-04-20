<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Indi extends Model
{
    protected $fillable = [
        'name',
        'year',
        'status'
    ];

    public function subindis()
    {
        return $this->hasMany(Subindi::class);
    }
}
