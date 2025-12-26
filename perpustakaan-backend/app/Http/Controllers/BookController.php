<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookController extends Controller
{
   public function index()
    {
        return response()->json(
            Book::all()
        );
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'created']);
    }

    public function show($id)
    {
        return response()->json([]);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['message' => 'updated']);
    }

    public function destroy($id)
    {
        return response()->json(['message' => 'deleted']);
    }
}
