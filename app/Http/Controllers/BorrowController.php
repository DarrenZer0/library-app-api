<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Book;
use App\Models\Borrow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class BorrowController extends Controller
{

    public function index()
    {
        $borrowRecords = Borrow::all(); 
    
        Log::info('Fetched borrow records:', ['records' => $borrowRecords]);

        return response()->json($borrowRecords);
    }

    public function user_index(Request $request)
    {
        $user_id = $request->user()->id;

        $borrowRecords = Borrow::where('user_id', $user_id)->get();

        return response()->json($borrowRecords);
    }
    
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
            $borrow->due_date = null;
            $borrow->status = 'Applied';
            $borrow->save();
            
            // Return success response
            return response()->json(['message' => 'A Borrow Request has been sent!'], 201);
        }
        
        // Return error if user is not authenticated
        return response()->json(['message' => 'Authentication required to borrow a book.'], 401);
    }

        public function updateStatus($userId, $borrowId, Request $request)
        {
        if ($request->user()->role !== 'admin') {
        return response()->json(['message' => 'Unauthorized.'], 403);
        }
    
    // Validate the status input
        $request->validate([
            'status' => 'required|in:Approved,Rejected,Returned'
        ]);
    
    // Find the borrow record
        $borrow = Borrow::where('id', $borrowId)->where('user_id', $userId)->first();
        if (!$borrow) {
            return response()->json(['message' => 'Borrow record not found or does not belong to this user.'], 404);
        }
    
    // Update the status
        $borrow->status = $request->status;

        if ($request->status === 'Approved') {
            $borrow->due_date = now()->addDays(6);

            $book = Book::find($borrow->book_id);
            if ($book && $book->quantity > 0) {
                $book->quantity -= 1;
                $book->save();
            } else {
                $borrow->status === 'Rejected';
                $borrow->due_date = null;
                return response()->json(['message' => 'Book quantity insufficient.'], 400);
            }

        } else if ($request->status === 'Rejected'){
            $borrow->due_date = null;
        } else if ($request->status === 'Returned') {
            $borrow->status = 'Returned';
            $borrow->returned_at = now();
        }

        $borrow->save();
    
        return response()->json(['message' => 'Status updated successfully.', 'data' => $borrow]);
        }
}
