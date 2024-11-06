<?php

namespace App\Http\Controllers;

use App\Exports\BooksExport;
use App\Imports\BooksImport;
use App\Models\Books;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class BooksController extends Controller
{
    public function index()
    {
        try {
            $books = Books::orderBy('book_cover', 'ASC')->get();
            return view('bookstore', ['books' => $books]);
        } catch (Exception $e) {
            return back()->with('error', 'There is an error fetching the book list');
        }
    }

    public function create()
    {
        try {
            return view('bookstore_components.create_book');
        } catch (Exception $e) {
            return back()->with('error', 'There is an error occured');
        }
    }

    public function store(Request $request)
    {
        $data = $request->only("book_name", "author", "book_cover");

        // FOR VALIDATION OF INPUTS
        $request->validate([
            'book_name' => [
                'required',
                Rule::unique('books_tbl')->where(function ($query) use ($data) {
                    return $query->where([
                        ['book_name', $data['book_name']],
                        ['author', $data['author']]
                    ]);
                }),
            ],
            'author' => 'required|string',
            'book_cover' => 'required|mimes:jpeg,png,gif|max:5080',
        ], [
            'book_name.required' => 'Book name is required',
            'book_name.unique' => 'Same book name and author is already added',
            'author.required' => 'Author is required',
            'book_cover.max' => 'Uploaded image must not exceed 5MB',
            'book_cover.required' => 'Please upload a cover photo',
            'book_cover.mimes' => 'The book cover must be a file of type: jpeg, png, gif.',
        ]);

        try {
            DB::beginTransaction();

            // FOR GENERATING UNIQUE ID
            $recentId = Books::select('book_id')->orderBy('created_at', 'DESC')->orderBy('book_id', 'DESC')->withTrashed()->first();
            $count = $recentId ? (int) explode('-', $recentId['book_id'])[1] : 0;
            $data['book_id'] = 'BOOK-' . str_pad($count + 1, 8, '0', STR_PAD_LEFT);

            // CHECK IF BOOK ID ALREADY EXIST
            $is_book_exist = Books::where('book_id', $data['book_id'])
                ->withTrashed()->first();
            if ($is_book_exist) {
                DB::rollBack();
                return back()->with('error', 'Book ID already exists');
            }

            // FOR FILE UPLOADING
            if (is_file($data['book_cover'])) {
                $data['book_cover'] = $this->uploadFile('book/images/' . $data['book_id'] . '/', $data['book_cover']);
            }

            // FOR ADDING BOOK
            $addBook = Books::firstOrCreate($data);
            if (!$addBook) {
                DB::rollBack();
                return back()->with('error', 'There is an error adding the book');
            }

            DB::commit();

            return back()->with('created', 'Successfully added a book!');
        } catch (Exception $e) {
            return back()->with('error', 'There is an error occured');
        }
    }

    public function show($book_id)
    {
        try {
            $books = Books::findOrFail($book_id);
            return view('bookstore_components.view_book', ['books' => $books]);
        } catch (Exception $e) {
            return back()->with('error', 'There is an error occured');
        }
    }

    public function edit($book_id)
    {
        try {
            $books = Books::findOrFail($book_id);
            return view('bookstore_components.update_book', ['books' => $books]);
        } catch (Exception $e) {
            return back()->with('error', 'There is an error occured');
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->only("book_name", "author", "book_cover");

        // FOR VALIDATION OF INPUTS
        $request->validate([
            'book_name' => [
                'required',
                Rule::unique('books_tbl')->where(function ($query) use ($data, $id) {
                    return $query->where([
                        ['book_id', '<>', $id],
                        ['book_name', $data['book_name']],
                        ['author', $data['author']]
                    ]);
                }),
            ],
            'author' => 'required|string',
            'book_cover' => 'nullable|mimes:jpeg,png,gif|max:5080',
        ], [
            'book_name.required' => 'Book name is required',
            'book_name.unique' => 'Same book name and author is already added',
            'author.required' => 'Author is required',
            'book_cover.max' => 'Uploaded image must not exceed 5MB',
            'book_cover.mimes' => 'The book cover must be a file of type: jpeg, png, gif.',
        ]);

        try {
            DB::beginTransaction();

            // CHECK IF BOOKS IS EXISTING
            $is_book_exist = Books::where('book_id', $id)->withTrashed()->first();
            if (!$is_book_exist) {
                DB::rollBack();
                return back()->with('error', 'Book does not exist!');
            }

            // FOR FILE UPLOADING
            if (is_file($data['book_cover'])) {
                $data['book_cover'] = $this->uploadFile('book/images/' . $id . '/', $data['book_cover']);
            } else {
                unset($data['book_cover']); // REMOVE COVER TO RETAIN CURRENT IMAGE
            }

            // FOR UPDATING BOOK
            $addBook = Books::where('book_id', $id)->update($data);
            if (!$addBook) {
                DB::rollBack();
                return back()->with('error', 'There is an error updating the book');
            }

            DB::commit();

            return redirect()->route('books.index')->with("updated", "Successfully updated the book!");
        } catch (Exception $e) {
            return back()->with('error', 'There is an error occured');
        }
    }

    public function destroy($id)
    {
        try {
            $is_book_exist = Books::where('book_id', $id)->first();

            if (!$is_book_exist) {
                return back()->with('error', 'There is an error archiving the book');
            }

            $is_book_exist->delete();

            return back()->with('deleted', "Book successfully archived!");
        } catch (Exception $e) {
            return back()->with('error', 'There is an error occured');
        }
    }

    public function handleImportFile(Request $request) {
        $data = $request->only("excel_file");

        // FOR VALIDATION OF INPUTS
        $request->validate([
            'excel_file' => 'required|mimes:csv,xlsx',
        ], [
            'excel_file.required' => 'Excel File is required',
            'excel_file.mimes' => 'Excel File must be a file of type: xlsx or csv',
        ]);

        try{
            DB::beginTransaction();

            // FETCH LATEST BOOK ID
            $recentId = Books::select('book_id')->orderBy('created_at', 'DESC')->orderBy('book_id', 'DESC')->withTrashed()->first();
            $count = $recentId ? (int) explode('-', $recentId['book_id'])[1] : 0;

            // FOR MAIN IMPORT PROCESS
            Excel::import(new BooksImport(isset($count) ? $count : 1), $data['excel_file']);

            DB::commit();
            return back()->with('created', "Books successfully imported!");
        }
        // CATCH FOR MAATWEBSITE IMPORT
        catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();
            foreach ($e->failures() as $key => $failure) {
                return back()->with('error', "ROW " . $failure->row() . ": " . $failure->errors()[0]);
            }
        }
        catch(Exception $e){
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function handleExportFile(){
        try{
            // session()->flash('exported', "Export successful. Please wait!");
            Redirect::back()->with('created', 'Export successful. Please wait!');

            // FOR MAIN EXPORT PROCESS
            return Excel::download(new BooksExport(), 'bookstore-library-'.date("m-d-Y-hiA").'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }
        // CATCH FOR MAATWEBSITE IMPORT
        catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();
            foreach ($e->failures() as $key => $failure) {
                return back()->with('error', "ROW " . $failure->row() . ": " . $failure->errors()[0]);
            }
        }catch(Exception $e){
            return back()->with('error', 'There is an error occured');
        }
    }

}
