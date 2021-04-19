<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(['Books' => Book::orderByDesc('created_at')->get()], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['required', 'string', 'min:3', 'max:255'],
            'category' => ['required', 'string', 'min:3', 'max:255'],
            'recomended_age' => ['required', 'integer', 'min:1', 'max:100'],
            'ISBN' => ['required', 'string', 'min:3', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }

        Auth::user()->books()->create($request->all());

        return response()->json([
            'success' => true,
            'data' => "Book created"
        ], 200);
    }
    public function showAllAuthor($id = null)
    {
        if ($id == null) {
            $user = Auth::user();
        } else {
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User with id ' . $id . ' not found'
                ], 400);
            }
        }

        return response()->json(['Books' => $user->books()->orderByDesc('created_at')->get()], 200);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Book with id ' . $id . ' not found'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $book->toArray()
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Book with id ' . $id . ' not found'
            ], 400);
        }
        $validator = Validator::make($request->all(), [
            'name' => ['nullable', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'min:3', 'max:255'],
            'category' => ['nullable', 'string', 'min:3', 'max:255'],
            'recomended_age' => ['nullable', 'integer', 'min:1', 'max:100'],
            'ISBN' => ['nullable', 'string', 'min:3', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }

        $book->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Book updated correctly'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book = Book::find($id);
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Book with id ' . $id . ' not found'
            ], 400);
        }

        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'Book deleted correctly'
        ], 200);
    }
}
