<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slot;
use App\Http\Requests\SlotRequest;

class SlotController extends Controller
{
    //
    public function index()
    {
        //
        $slots = Slot::latest()->get();
        return response()->json($slots);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SlotRequest $request)
    {
        //
        $data = $request->validated();
        $slot = Slot::create($data);
        return response()->json($slot, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Slot $slot)
    {
        //
        return response()->json($slot);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(SlotRequest $request, Slot $slot)
    {
        //
        $data = $request->validated();
        $slot->update($data);
        return response()->json($slot);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Slot $slot)
    {

        $slot->delete();
        return response()->json([
            'success' => true,
            'message' => 'Slot deleted successfully'
        ]);
    }
}
