<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Book;
use App\Models\Borrow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BorrowController extends Controller
{
    public function borrow(Request $request, $id)
{
    $data = Book::find($id);
    if (!$data) {
        return response()->json(['error' => 'Book not found!'], 404);
    }

    $quantity = $data->quantity;

    // Check if the book is available
    if ($quantity < 1) 
    {
        return response()->json(['message' => 'No Books Available!'], 400);

    }
    
    if(Auth::id())
    {
    $user_id = Auth::id();
    $borrow = new Borrow;
    $borrow->book_id = $id;
    $borrow->user_id = $user_id;
    $borrow->borrowed_at = now();
    $borrow->due_date = Carbon::now()->addDays(6);
    $borrow->status = 'Applied';
    $borrow->save();

    // Return success response
    return response()->json(['message' => 'A Borrow Request has been sent!'], 201);
    }

    // Return error if user is not authenticated
    return response()->json(['message' => 'Authentication required to borrow a book.'], 401);
}
}
