<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Http\Requests\StoreContentRequest;
use App\Http\Requests\UpdateContentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContentController extends Controller
{
    /**
     * แสดงรายการเนื้อหาทั้งหมด (สำหรับ Admin)
     */
    public function index(Request $request)
    {
        $query = Content::with('author:id,name,email');

        // กรองตาม section
        if ($request->has('section')) {
            $query->where('section', $request->section);
        }

        // กรองตาม status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // ค้นหา
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('content', 'LIKE', "%{$search}%");
            });
        }

        // เรียงลำดับ
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $contents = $query->get();
        return response()->json([
            'success' => true,
            'data' => $contents
        ]);
    }

    /**
     * สร้างเนื้อหาใหม่
     */
    public function store(StoreContentRequest $request)
    {
        $data = $request->validated();
        $data['author_id'] = auth()->id();

        // อัพโหลดรูปภาพ
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('contents/images', 'public');
            $data['image'] = $imagePath;
        }

        // อัพโหลดไฟล์
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('contents/files', 'public');
            $data['file_url'] = $filePath;
        }

        $content = Content::create($data);
        $content->load('author:id,name,email');

        return response()->json([
            'success' => true,
            'message' => 'สร้างเนื้อหาสำเร็จ',
            'data' => $content
        ], 201);
    }

    /**
     * แสดงรายละเอียดเนื้อหา
     */
    public function show($id)
    {
        $content = Content::with('author:id,name,email')->find($id);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบเนื้อหา'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $content
        ]);
    }

    /**
     * แก้ไขเนื้อหา
     */
    public function update(UpdateContentRequest $request, $id)
    {
        $content = Content::find($id);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบเนื้อหา'
            ], 404);
        }

        $data = $request->validated();

        // ลบรูปภาพถ้าได้รับคำสั่ง
        if ($request->boolean('remove_image') && $content->image) {
            Storage::disk('public')->delete($content->image);
            $data['image'] = null;
        }

        // ลบไฟล์ถ้าได้รับคำสั่ง
        if ($request->boolean('remove_file') && $content->file_url) {
            Storage::disk('public')->delete($content->file_url);
            $data['file_url'] = null;
        }

        // อัพโหลดรูปภาพใหม่
        if ($request->hasFile('image')) {
            // ลบรูปเก่า
            if ($content->image) {
                Storage::disk('public')->delete($content->image);
            }
            $imagePath = $request->file('image')->store('contents/images', 'public');
            $data['image'] = $imagePath;
        }

        // อัพโหลดไฟล์ใหม่
        if ($request->hasFile('file')) {
            // ลบไฟล์เก่า
            if ($content->file_url) {
                Storage::disk('public')->delete($content->file_url);
            }
            $filePath = $request->file('file')->store('contents/files', 'public');
            $data['file_url'] = $filePath;
        }

        // ลบ keys ที่ไม่ต้องการ update
        unset($data['remove_image'], $data['remove_file']);

        $content->update($data);
        $content->load('author:id,name,email');

        return response()->json([
            'success' => true,
            'message' => 'แก้ไขเนื้อหาสำเร็จ',
            'data' => $content
        ]);
    }

    /**
     * ลบเนื้อหา
     */
    public function destroy($id)
    {
        $content = Content::find($id);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบเนื้อหา'
            ], 404);
        }

        // ลบไฟล์ที่เกี่ยวข้อง
        if ($content->image) {
            Storage::disk('public')->delete($content->image);
        }
        if ($content->file_url) {
            Storage::disk('public')->delete($content->file_url);
        }

        $content->delete();

        return response()->json([
            'success' => true,
            'message' => 'ลบเนื้อหาสำเร็จ'
        ]);
    }

    /**
     * ดึงรายการ sections ทั้งหมด
     */
    public function getSections()
    {
        return response()->json([
            'success' => true,
            'data' => Content::SECTIONS
        ]);
    }
}
