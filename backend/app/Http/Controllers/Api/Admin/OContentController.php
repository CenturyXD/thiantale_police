<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\OContent;
use App\Http\Requests\StoreOContentRequest;
use App\Http\Requests\UpdateOContentRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contents = OContent::with(['os', 'author:id,name,email'])
            ->latest()
            ->get();

        return response()->json($contents);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOContentRequest $request)
    {
        $data = $request->validated();
        $data['author_id'] = Auth::id();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('o_contents/images', 'public');
        }

        if ($request->hasFile('file')) {
            $data['file_url'] = $request->file('file')->store('o_contents/files', 'public');
        }

        $content = OContent::create($data);

        return response()->json($content->load(['os', 'author:id,name,email']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(OContent $oContent)
    {
        return response()->json($oContent->load(['os', 'author:id,name,email']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOContentRequest $request, OContent $oContent)
    {
        $data = $request->validated();

        if ($request->boolean('remove_image') && $oContent->image) {
            Storage::disk('public')->delete($oContent->image);
            $data['image'] = null;
        }

        if ($request->boolean('remove_file') && $oContent->file_url) {
            Storage::disk('public')->delete($oContent->file_url);
            $data['file_url'] = null;
        }

        if ($request->hasFile('image')) {
            if ($oContent->image) {
                Storage::disk('public')->delete($oContent->image);
            }
            $data['image'] = $request->file('image')->store('o_contents/images', 'public');
        }

        if ($request->hasFile('file')) {
            if ($oContent->file_url) {
                Storage::disk('public')->delete($oContent->file_url);
            }
            $data['file_url'] = $request->file('file')->store('o_contents/files', 'public');
        }

        unset($data['remove_image'], $data['remove_file']);

        $oContent->update($data);

        return response()->json($oContent->load(['os', 'author:id,name,email']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OContent $oContent)
    {
        if ($oContent->image) {
            Storage::disk('public')->delete($oContent->image);
        }

        if ($oContent->file_url) {
            Storage::disk('public')->delete($oContent->file_url);
        }

        $oContent->delete();

        return response()->json([
            'success' => true,
            'message' => 'ลบเนื้อหาสำเร็จ',
        ]);
    }
}
