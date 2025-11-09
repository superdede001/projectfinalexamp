<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;

Route::get('/books', [BookController::class, 'index'])->name('books.index');

Route::get('/', [HomeController::class, 'index'])->name('home');
// Buku
Route::resource('books', BookController::class);
// Rating
Route::get('ratings/{book}/create', [RatingController::class, 'create'])->name('ratings.create');
Route::post('ratings', [RatingController::class, 'store'])->name('ratings.store');

// Author dan Category 
Route::resource('categories', CategoryController::class)->only(['index']);
Route::get('/authors', [AuthorController::class, 'index'])->name('authors.index');

Route::resource('home', HomeController::class)->only(['index']);
