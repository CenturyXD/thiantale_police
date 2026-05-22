<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Http\Requests\StoreTopicRequest;
use App\Http\Requests\UpdateTopicRequest;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $topics = Topic::with('subindi')->latest()->get();
        return response()->json($topics);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTopicRequest $request)
    {
        $data = $request->validated();
        $topic = Topic::create($data);

        return response()->json($topic->load('subindi'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Topic $topic)
    {
        return response()->json($topic->load('subindi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTopicRequest $request, Topic $topic)
    {
        $data = $request->validated();
        $topic->update($data);

        return response()->json($topic->load('subindi'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Topic $topic)
    {
        $topic->delete();

        return response()->json([
            'success' => true,
            'message' => 'ลบหัวข้อสำเร็จ',
        ]);
    }
    /**
     * แสดงข้อมูลทั้งหมดพร้อม relations
     */
    public function allWithRelations()
    {
        $topics = Topic::with(['indi', 'subindi', 'os', 'oContents'])->get();
        return response()->json([
            'success' => true,
            'data' => $topics
        ]);
    }

    /**
     * แสดงข้อมูลตาม subindi_id
     */
    public function getByTopicid()
    {
        request()->validate([
            'Topic_id' => 'required|exists:topics,id',
        ]);
        $topic_id = request()->input('Topic_id');
        $topics = Topic::with(['subindi'])->where('id', $topic_id)->latest()->get();
        return response()->json($topics);
    }
}
