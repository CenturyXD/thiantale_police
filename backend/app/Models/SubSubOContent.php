<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubSubOContent extends Model
{
    use HasFactory;

    protected $table = 'main_sub_o_contents';

    protected $fillable = [
        'o_content_id',
        'name',
        'status',
        'created_at',
        'updated_at'
    ];
}

