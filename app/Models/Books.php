<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Books extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'books_tbl';
    protected $primaryKey = 'book_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'book_id',
        'book_name',
        'author',
        'book_cover'
    ];

    // FOR ADDING URL WHEN FETCHING COVER ITEM
    public function getCoverAttribute ($value) {
        return !$value ? null : env('APP_URL').'/'.$value;
    }
}
