<?php

use App\Http\Controllers\BooksController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', [BooksController::class, 'index'])->name('bookstore');
Route::post('books/import', [BooksController::class, 'handleImportFile'])->name('import');
Route::get('books/export', [BooksController::class, 'handleExportFile'])->name('export');

Route::resource('books', BooksController::class);


