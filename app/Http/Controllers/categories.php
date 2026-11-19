<?php

namespace App\Http\Controllers;

use App\Models\categories as ModelsCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class categories extends Controller
{
    public function all_category(Request $request) {
        return response()->json([
            'data' => ModelsCategories::all()
        ]);
    }

    public function add_category(Request $request) {
        $valid = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name',
            'description' => 'required'
        ]);

        if ($valid->fails()) {
            return response()->json($valid->errors(),422);
        }

        $maked_category = ModelsCategories::create([
            'name' => $request->name,
            'slug' => strtolower($request->name),
            'description' => $request->description
        ]);

        return response()->json([
            'data' => $maked_category
        ],201);
    }


}
