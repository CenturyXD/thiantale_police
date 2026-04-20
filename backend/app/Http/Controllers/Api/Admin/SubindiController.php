<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subindi;
use App\Http\Requests\StoreSubindiRequest;
use App\Http\Requests\UpdateSubindiRequest;

class SubindiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subindis = Subindi::with('indi')->latest()->get();
        return response()->json($subindis);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubindiRequest $request)
    {
        $data = $request->validated();
        $subindi = Subindi::create($data);

        return response()->json($subindi->load('indi'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Subindi $subindi)
    {
        return response()->json($subindi->load('indi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubindiRequest $request, Subindi $subindi)
    {
        $data = $request->validated();
        $subindi->update($data);

        return response()->json($subindi->load('indi'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subindi $subindi)
    {
        $subindi->delete();

        return response()->json([
            'success' => true,
            'message' => 'ลบตัวชี้วัดย่อยสำเร็จ',
        ]);
    }
}
