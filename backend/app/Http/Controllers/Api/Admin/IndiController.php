<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Indi;
use App\Http\Requests\StoreIndiRequest;
use App\Http\Requests\UpdateIndiRequest;

class IndiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $indis = Indi::with('subindis')->latest()->get();
        return response()->json($indis);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIndiRequest $request)
    {
        $data = $request->validated();
        $indi = Indi::create($data);
        return response()->json($indi->load('subindis'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Indi $indi)
    {
        return response()->json($indi->load('subindis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIndiRequest $request, Indi $indi)
    {
        $data = $request->validated();
        $indi->update($data);

        return response()->json($indi->load('subindis'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Indi $indi)
    {
        $indi->delete();

        return response()->json([
            'success' => true,
            'message' => 'ลบตัวชี้วัดสำเร็จ',
        ]);
    }
}
