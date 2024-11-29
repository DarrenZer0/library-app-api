<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
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
            'category' => 'required|integer|exists:categories,id',
            'book_img' => 'required|image|mimes:jpeg,png,jpg|max:2048',
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
            if ($request->hasFile('book_img')) 
            {
                $book_img = time() . '.' . $request->book_img->extension();
                $request->book_img->move(public_path('img'), $book_img);

                $book = new Book;
                $book->title=$request->title;
                $book->author=$request->author;
                $book->description=$request->description;
                $book->quantity=$request->quantity;
                $book->category_id=$request->category;
                $book->book_img=$book_img;

                $book->save();

                $data = 
                [
                    'status' => 200,
                    'message' => 'Book Added Successfully'
                ];

                return response()->json($data, 200);

            }
        }
    }

    public function edit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'quantity' => 'required|integer|max:255',
            'category' => 'required|integer|exists:categories,category',
            'book_img' => 'required|image|mimes:jpeg,png,jpg|max:2048',
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
            if ($request->hasFile('book_img')) 
            {
                $book_img = time() . '.' . $request->book_img->extension();
                $request->book_img->move(public_path('img'), $book_img);
                $book = Book::find($id);
                $book->title=$request->title;
                $book->author=$request->author;
                $book->description=$request->description;
                $book->quantity=$request->quantity;
                $book->category_id=$request->category;
                $book->book_img=$book_img;

                $book->save();

                $data = 
            [
                    'status' => 200,
                    'message' => 'Book Updated Successfully'
            ];
            return response()->json($data, 200);
            }
        
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
