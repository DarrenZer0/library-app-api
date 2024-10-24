<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function index()
    {
        $book = Book::all();
        $data = 
        [
            'status' => 200,
            'book'=> $book
        ];

        return response()->json($data, 200);
    }

    public function add_books(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'quantity' => 'required|integer|max:255',
        ]);

        if($validator->fails())
        {
            $data =
            [
                "status" => 422,
                "message" => $validator->messages()
            ];
            return response()->json($data, 422);
        }
        else
        {
        $book = new Book;
        $book->title=$request->title;
        $book->author_name=$request->author;
        $book->quantity=$request->quantity;
        $book->description=$request->description;

        $book->save();

        $data = 
        [
            'status' => 200,
            'message' => 'Book Added Successfully'
        ];
        return response()->json($data, 200);

        }
    }

    public function edit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'quantity' => 'required|integer|max:255',
        ]);

        if($validator->fails())
        {
            $data =
            [
                "status" => 422,
                "message" => $validator->messages()
            ];
            return response()->json($data, 422);
        }
        else
        {
        $book = Book::find($id);
        $book->title=$request->title;
        $book->author_name=$request->author;
        $book->quantity=$request->quantity;
        $book->description=$request->description;

        $book->save();

        $data = 
        [
            'status' => 200,
            'message' => 'Book Updated Successfully'
        ];
        return response()->json($data, 200);
        
        }
    }

    public function delete($id)
    {
       $book = Book::find($id);
       $book-> delete();

       $data =
       [
        'status' => 200,
        'message' => 'Book Deleted Successfully'
       ];

       return response()->json($data, 200);
    }
}
