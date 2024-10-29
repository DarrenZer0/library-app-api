<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'author_name',
        'description',
        'quantity',
        'category',
        'book_img,',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
