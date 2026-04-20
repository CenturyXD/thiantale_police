<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OContent extends Model
{
    protected $fillable = [
        'os_id',
        'title',
        'content',
        'section',
        'image',
        'file_url',
        'publish_date',
        'author_id',
        'view_count',
        'order',
        'status'
    ];

    protected $casts = [
        'publish_date' => 'date',
        'view_count' => 'integer',
        'order' => 'integer',
    ];

    public function os()
    {
        return $this->belongsTo(Os::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
