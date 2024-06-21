<?php

namespace App\Http\Controllers;

use App\Http\Requests\Violation\ViolationRequest;
use App\Models\ViolationPlace;
use Illuminate\Http\Request;

class ViolationPlaceController extends Controller
{
    public function store(ViolationRequest $request)
    {
        $data = $request->validated();
        $violation = ViolationPlace::create($data);

        return response()->json($violation, 201);
    }

    public function index()
    {
        $violations = ViolationPlace::all();
        return response()->json($violations);
    }

//    public function show($id)
//    {
//        $violation = ViolationPlace::findOrFail($id);
//        return response()->json($violation);
//    }

    public function update(Request $request, ViolationPlace $violation)
    {
        $violation->update($request->all());

        return response()->json($violation);
    }

    public function destroy(ViolationPlace $violation)
    {
        $violation->delete();

        return response()->json(null, 204);
    }
}

