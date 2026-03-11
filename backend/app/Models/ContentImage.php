<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentImage extends Model
{
    use HasFactory;

    protected $appends = [
        'image_url',
    ];

    protected $fillable = [
        'content_id',
        'image_path',
        'sort_order',
    ];

    protected $casts = [
        'content_id' => 'integer',
        'sort_order' => 'integer',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset($this->image_path) : null;
    }
}
