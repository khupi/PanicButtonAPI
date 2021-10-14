<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Panic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PanicResource;

class PanicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $panics = Panic::all();
        return response([ 'panics' => PanicResource::collection($panics), 'message' => 'Action completed successfully'], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'longitude' => 'required|max:255',
            'latitude' => 'required|max:255',
            'panic_type' => 'nullable',
            'details' => 'nullable'
        ]);

        if($validator->fails())
        {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }
        
        $panic = Panic::create($data);

        return response(['panic' => new PanicResource($panic),'message' => 'Panic raised successfully'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Panic  $panic
     * @return \Illuminate\Http\Response
     */
    public function show(Panic $panic)
    {
        return response(['panic' => new PanicResource($panic), 'message' => 'Retrieved successfully'], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Panic  $panic
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Panic $panic)
    {
        $panic->update($request->all());

        return response(['panic' => new PanicResource($panic), 'message' => 'Update successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Panic  $panic
     * @return \Illuminate\Http\Response
     */
    public function destroy(Panic $panic)
    {
        $panic->delete();
        return response(['message' => 'Deleted']);
    }
}
