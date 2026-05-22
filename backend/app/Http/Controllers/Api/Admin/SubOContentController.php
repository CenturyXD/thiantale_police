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
}
