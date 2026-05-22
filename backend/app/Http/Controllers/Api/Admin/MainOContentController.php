<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMainOContentRequest;
use App\Http\Requests\UpdateMainOContentRequest;
use App\Models\MainOContent;

class MainOContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $mainOContents = MainOContent::with(['oContent.os.topic.subIndi'])->latest()->get();
        return response()->json($mainOContents);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMainOContentRequest $request)
    {
        //
        $data = $request->validated();
        $data['author_id'] = auth()->id();
        $mainOContent = MainOContent::create($data);
        return response()->json($mainOContent->load(['oContent.os.topic.subIndi']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $mainOContent = MainOContent::with(['oContent.os.topic.subIndi'])->findOrFail($id);
        return response()->json($mainOContent);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMainOContentRequest $request, string $id)
    {
        //
        $mainOContent = MainOContent::findOrFail($id);
        $data = $request->validated();
        $mainOContent->update($data);
        return response()->json($mainOContent->load(['oContent.os.topic.subIndi']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $mainOContent = MainOContent::findOrFail($id);
        $mainOContent->delete();
        return response()->json(null, 204);
    }

    public function getByOContentId()
    {
         request()->validate([
            'main_o_content_id' => 'required',
        ]);

        $mainOContent = MainOContent::with(['oContent.os.topic.subIndi'])
            ->where('o_content_id', request('main_o_content_id'))
            ->get();
        return response()->json($mainOContent);
    }
}
