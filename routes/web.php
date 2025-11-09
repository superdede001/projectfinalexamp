<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\TopAuthorsController;

Route::get('/', fn() => redirect('/books'));
Route::get('/books', [BookController::class, 'index']);
Route::get('/authors/top-authors', [AuthorController::class, 'topAuthors']);
Route::get('/ratings/create', [RatingController::class, 'create']);
Route::post('/ratings', [RatingController::class, 'store']);
Route::get('/authors/top-authors', [TopAuthorsController::class, 'index']);
Route::get('/ratings/create', [RatingController::class, 'create'])->name('ratings.create');
Route::post('/ratings', [RatingController::class, 'store'])->name('ratings.store');