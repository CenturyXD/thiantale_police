<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Os extends Model
{
    protected $table = 'os';

    protected $fillable = [
        'name',
        'status',
        'topic_id'
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }
}
