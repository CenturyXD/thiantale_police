<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;
use App\Http\Requests\SectionRequest;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $sections = Section::latest()->get();
        return response()->json($sections);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(SectionRequest $request)
    {
        //
        $data = $request->validated();
        $section = Section::create($data);
        return response()->json($section, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Section $section)
    {
        //

        return response()->json($section);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SectionRequest $request, Section $section)
    {
        //
        $data = $request->validated();
        $section->update($data);
        return response()->json($section);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Section $section)
    {
        //
        $section->delete();
        return response()->json([
            'success' => true,
            'message' => 'section deleted successfully',
        ]);
    }
}
