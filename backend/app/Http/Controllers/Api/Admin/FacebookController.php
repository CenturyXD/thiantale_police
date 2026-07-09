<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facebook;
use Illuminate\Http\Request;
use App\Http\Requests\FacebookRequest;
class FacebookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $facebook = Facebook::all();
        return response()->json($facebook);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FacebookRequest $request)
    {
        //
        $facebook = Facebook::create($request->all());
        return response()->json($facebook);
    }

    /**
     * Display the specified resource.
     */
    public function show(Facebook $facebook)
    {
        //
        return response()->json($facebook);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Facebook $facebook)
    {
        //
        $facebook->update($request->all());
        return response()->json($facebook);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Facebook $facebook)
    {
        //
        $facebook->delete();
        return response()->json($facebook);
    }
}
