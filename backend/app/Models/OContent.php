<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OContent extends Model
{
    protected $fillable = [
        'name',
        'status',
        'os_id',
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

    public function subOContents()
    {
        return $this->hasMany(SubOContent::class, 'o_content_id');
    }
}
