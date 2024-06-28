<?php

namespace App\Http\Controllers;

use App\Models\CenterOfCost;
use Illuminate\Http\Request;

class CenterOfCostController extends Controller
{
    public function index()
    {
        return CenterOfCost::all();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $centerOfCost = CenterOfCost::create($validatedData);
        return response()->json($centerOfCost, 201);
    }

    public function show($id)
    {
        $centerOfCost = CenterOfCost::findOrFail($id);
        return response()->json($centerOfCost);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $centerOfCost = CenterOfCost::findOrFail($id);
        $centerOfCost->update($validatedData);
        return response()->json($centerOfCost, 200);
    }

    public function destroy($id)
    {
        $centerOfCost = CenterOfCost::findOrFail($id);
        $centerOfCost->delete();
        return response()->json(null, 204);
    }
}
