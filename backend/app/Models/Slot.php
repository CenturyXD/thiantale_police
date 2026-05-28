<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    use HasFactory;
    protected $table = 'slot';

    protected $fillable = [
        'sec_id',
        'name',
        'type'
    ];

    public function section()
    {
        return $this->belongsTo(Section::class, 'sec_id');
    }
}
