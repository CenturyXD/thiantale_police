<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ContentImage;

class Content extends Model
{
    use HasFactory;

    protected $appends = [
        'image_url',
        'file_download_url',
    ];

    protected $fillable = [
        'title',
        'content',
        'section',
        'image',
        'file_url',
        'status',
        'publish_date',
        'author_id',
        'view_count',
        'order',
        'slot'
    ];

    protected $casts = [
        'publish_date' => 'date',
        'view_count' => 'integer',
        'order' => 'integer'
    ];

    /**
     * Sections ที่สามารถใช้ได้
     */
    public const SECTIONS = [
        'news' => 'ข่าวสาร',
        'personnel' => 'ข้อมูลเจ้าหน้าที่',
        'announcement' => 'ประกาศ',
        'e_service' => 'E-Service',
        'statistics' => 'สถิติ',
        'education' => 'การศึกษา',
        'qa' => 'Q&A',
        'gallery' => 'แกลเลอรี',
        'documents' => 'เอกสาร',
        'other' => 'อื่นๆ'
    ];

    /**
     * ความสัมพันธ์กับผู้เขียน
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function bodyImages()
    {
        return $this->hasMany(ContentImage::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Scope: เฉพาะเนื้อหาที่เผยแพร่แล้ว
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope: กรองตาม section
     */
    public function scopeSection($query, $section)
    {
        return $query->where('section', $section);
    }

    /**
     * เพิ่มจำนวนการดู
     */
    public function incrementViews()
    {
        $this->increment('view_count');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset($this->image) : null;
    }

    public function getFileDownloadUrlAttribute(): ?string
    {
        return $this->file_url ? asset($this->file_url) : null;
    }
}
