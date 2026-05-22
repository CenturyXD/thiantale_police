<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSubOContentRequest;
use App\Http\Requests\UpdateSubOContentRequest;
use App\Models\SubOContent;

class SubOContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $subOContents = SubOContent::with(['oContent.os.topic.subIndi'])->latest()->get();
        return response()->json($subOContents);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubOContentRequest $request)
    {
        //
        $data = $request->validated();
        $data['author_id'] = auth()->id();

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

        $subOContent = SubOContent::create($data);
        return response()->json($subOContent->load(['oContent.os.topic.subIndi']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $subOContent = SubOContent::with(['oContent.os.topic.subIndi'])->findOrFail($id);
        return response()->json($subOContent);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubOContentRequest $request, string $id)
    {
        //
        $subOContent = SubOContent::findOrFail($id);

        $data = $request->validated();

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

        $subOContent->update($data);
        return response()->json($subOContent->load(['oContent.os.topic.subIndi']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subOContent = SubOContent::findOrFail($id);
        $subOContent->delete();
        return response()->json(null, 204);
    }

    public function getBySubOContentid()
    {
        request()->validate([
            'sub_o_content_id' => 'required',
        ]);

        $sub_o_content_id = request()->input('sub_o_content_id');
        $subOContents = SubOContent::with(['oContent.os.topic.subIndi'])
            ->where('main_sub_o_content_id', $sub_o_content_id)
            ->latest()
            ->get();
        return response()->json($subOContents);

    }
}
