<?php

namespace App\Imports;

use App\Models\Books;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BooksImport implements ToModel, WithBatchInserts, SkipsEmptyRows, WithChunkReading, WithHeadingRow, WithStartRow, WithValidation
{

    private $book_id;

    public function __construct($book_id)
    {
        $this->book_id = $book_id;
    }

    public function model(array $row)
    {
        try{

            // FOR CHECKING DUPLICATE ID
            $current_book_id = 'BOOK-' . str_pad($this->book_id += 1, 8, '0', STR_PAD_LEFT);
            $is_book_exist = Books::where('book_id', $current_book_id)->withTrashed()->first();
            if ($is_book_exist) {
                throw new \Exception('Book ID already exist!');
            }

            // FOR CHECKING DUPLICATE ENTRY
            $book_validation = Books::where(function ($query) use ($row) {
                return $query->where([
                    ['book_name', $row['book_name']],
                    ['author', $row['author']]
                ]);
            })->first();

            if($book_validation){
                throw new \Exception('Same book name and author already exist!');
            }

            return new Books([
                'book_id' => $current_book_id,
                'book_name' => $row['book_name'],
                'author' => $row['author'],
            ]);

        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }

    }

    public function startRow(): int
    {
        return 2;
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    // FOR VALIDATION RULE
    public function rules(): array
    {
        return [
            'book_name' => [
                'required',
                // Rule::unique('books_tbl')->where(function ($query){
                //     return $query->where([
                //         ['book_name', request()->input('book_name')],
                //         ['author', request()->input('author')]
                //     ]);
                // }),
            ],
            'author' => 'required|string',
        ];
    }

}
