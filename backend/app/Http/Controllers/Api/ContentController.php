<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    /**
     * ดึงเนื้อหาสำหรับแสดงหน้าเว็บ (Public)
     */
    public function index(Request $request)
    {
        $query = Content::published()->with('author:id,name');

        // กรองตาม section (บังคับ)
        if ($request->has('section')) {
            $query->section($request->section);
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
        $query->orderBy('order', 'asc')
            ->orderBy('publish_date', 'desc');

        $perPage = $request->get('per_page', 12);
        $contents = $query->paginate($perPage);

        return response()->json($contents);
    }

    /**
     * ดูรายละเอียดเนื้อหา
     */
    public function show($id)
    {
        $content = Content::published()
            ->with('author:id,name')
            ->find($id);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบเนื้อหา'
            ], 404);
        }

        // เพิ่มจำนวนการดู
        $content->incrementViews();

        return response()->json([
            'success' => true,
            'data' => $content
        ]);
    }

    /**
     * ดึงเนื้อหาล่าสุด
     */
    public function latest(Request $request)
    {
        $limit = $request->get('limit', 5);
        $section = $request->get('section');

        $query = Content::published()->with('author:id,name');

        if ($section) {
            $query->section($section);
        }

        $contents = $query->orderBy('publish_date', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $contents
        ]);
    }

    /**
     * ดึงเนื้อหายอดนิยม (ดูมากที่สุด)
     */
    public function popular(Request $request)
    {
        $limit = $request->get('limit', 5);
        $section = $request->get('section');

        $query = Content::published()->with('author:id,name');

        if ($section) {
            $query->section($section);
        }

        $contents = $query->orderBy('view_count', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $contents
        ]);
    }
}
