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

        //กรองตาม slot
        if ($request->has('slot')) {
            $query->where('slot', $request->slot);
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

        // อัพโหลดรูปภาพไปที่ public/contents/images
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('contents/images'), $imageName);
            $data['image'] = 'contents/images/' . $imageName;
        }

        // อัพโหลดไฟล์ไปที่ public/contents/files
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('contents/files'), $fileName);
            $data['file_url'] = 'contents/files/' . $fileName;
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
            $oldImagePath = public_path($content->image);
            if (file_exists($oldImagePath)) {
                @unlink($oldImagePath);
            }
            $data['image'] = null;
        }

        // ลบไฟล์ถ้าได้รับคำสั่ง
        if ($request->boolean('remove_file') && $content->file_url) {
            $oldFilePath = public_path($content->file_url);
            if (file_exists($oldFilePath)) {
                @unlink($oldFilePath);
            }
            $data['file_url'] = null;
        }

        // อัพโหลดรูปภาพใหม่
        if ($request->hasFile('image')) {
            // ลบรูปเก่า
            if ($content->image) {
                $oldImagePath = public_path($content->image);
                if (file_exists($oldImagePath)) {
                    @unlink($oldImagePath);
                }
            }
            $image = $request->file('image');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('contents/images'), $imageName);
            $data['image'] = 'contents/images/' . $imageName;
        }

        // อัพโหลดไฟล์ใหม่
        if ($request->hasFile('file')) {
            // ลบไฟล์เก่า
            if ($content->file_url) {
                $oldFilePath = public_path($content->file_url);
                if (file_exists($oldFilePath)) {
                    @unlink($oldFilePath);
                }
            }
            $file = $request->file('file');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('contents/files'), $fileName);
            $data['file_url'] = 'contents/files/' . $fileName;
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
