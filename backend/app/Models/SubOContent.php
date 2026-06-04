<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubOContent extends Model
{

    protected $table = 'sub_o_contents';
    protected $fillable = [
        'main_sub_o_content_id',
        'title',
        'content',
        'section',
        'image',
        'file_url',
        'status',
        'publish_date',
        'order',
        'created_at',
        'author_id',
        'updated_at',
        'url',
    ];

    public function oContent()
    {
        return $this->belongsTo(OContent::class, 'o_content_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function mainOContents()
    {
        return $this->hasMany(MainOContent::class, 'main_o_contents_id');
    }

}
