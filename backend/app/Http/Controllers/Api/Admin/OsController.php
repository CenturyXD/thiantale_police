<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Os;
use App\Http\Requests\StoreOsRequest;
use App\Http\Requests\UpdateOsRequest;

class OsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $os = Os::with('topic')->latest()->get();
        return response()->json($os);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOsRequest $request)
    {
        $data = $request->validated();
        $os = Os::create($data);

        return response()->json($os->load('topic'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Os $o)
    {
        return response()->json($o->load('topic'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOsRequest $request, Os $o)
    {
        $data = $request->validated();
        $o->update($data);

        return response()->json($o->load('topic'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Os $o)
    {
        $o->delete();

        return response()->json([
            'success' => true,
            'message' => 'ลบหัวข้อย่อย os สำเร็จ',
        ]);
    }
}
