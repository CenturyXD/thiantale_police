<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainOContent extends Model
{
    use HasFactory;

    protected $table = 'main_o_contents';
    protected $fillable = [
        'os_id',
        'name',
        'status',
        'o_content_id',
    ];

    public function os()
    {
        return $this->belongsTo(Os::class, 'os_id');
    }

    public function oContent()
    {
        return $this->belongsTo(OContent::class, 'o_content_id');
    }
    public function subOContents()
    {
        return $this->hasMany(SubOContent::class, 'main_sub_o_content_id');
    }
}
