<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // list buku (member boleh lihat; admin juga)
    public function index()
    {
        return response()->json(Book::orderBy('id','desc')->get(), 200);
    }

    public function show($id)
    {
        $book = Book::find($id);
        if (!$book) return response()->json(['message' => 'Book not found'], 404);
        return response()->json($book, 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'api_book_id' => 'nullable|string',
            'title' => 'required|string',
            'author' => 'nullable|string',
            'publisher' => 'nullable|string',
            'year' => 'nullable|integer',
            'stock' => 'required|integer|min:0',
        ]);

        if (!empty($data['api_book_id'])) {
        $exist = Book::where('api_book_id', $data['api_book_id'])->first();
        if ($exist) {
            $exist->update([
                'title'  => $data['title'],
                'author' => $data['author'] ?? $exist->author,
            ]);
            return response()->json(['message' => 'Buku sudah ada di inventaris', 'data' => $exist], 200);
        }
    }

    $book = Book::create([
        'api_book_id' => $data['api_book_id'] ?? null,
        'title'       => $data['title'],
        'author'      => $data['author'] ?? null,
        'stock'       => $data['stock'] ?? 0,
    ]);

    return response()->json(['message' => 'Buku ditambahkan ke inventaris', 'data' => $book], 201);
    }

    public function update(Request $request, $id)
    {
        $book = Book::find($id);
        if (!$book) return response()->json(['message' => 'Book not found'], 404);

        $data = $request->validate([
            'api_book_id' => 'nullable|string',
            'title' => 'sometimes|required|string',
            'author' => 'nullable|string',
            'publisher' => 'nullable|string',
            'year' => 'nullable|integer',
            'stock' => 'sometimes|required|integer|min:0',
            'location' => 'nullable|string'
        ]);

        $book->update($data);
        return response()->json(['message' => 'Book updated', 'data' => $book], 200);
    }

    public function destroy($id)
    {
        $book = Book::find($id);
        if (!$book) return response()->json(['message' => 'Book not found'], 404);

        $book->delete();
        return response()->json(['message' => 'Book deleted'], 200);
    }
}
