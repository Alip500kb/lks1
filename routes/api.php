<?php

use App\Http\Controllers\authenticate;
use App\Http\Controllers\categories;
use App\Http\Controllers\items;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [authenticate::class, 'singup'])->middleware(['guest','throttle:3.1']);
Route::post('/login', [authenticate::class, 'singin'])->middleware('guest');
Route::post('/logout', [authenticate::class, 'singout'])->middleware('auth:sanctum');
Route::get('/me', [authenticate::class, 'getme'])->middleware('auth:sanctum');

Route::get('/categories', [categories::class, 'all_category']);
Route::post('/categories', [categories::class, 'add_category'])->middleware('auth:sanctum');

Route::get('/items', [items::class, 'all_item'])->middleware('guest');
Route::get('/items/{id}', [items::class, 'by_item']);
Route::post('/items', [items::class, 'up_item'])->middleware('auth:sanctum');
Route::put('/items/{id}', [items::class, 'put_item'])->middleware('auth:sanctum');
Route::patch('/items/{id}', [items::class, 'patch_item'])->middleware('auth:sanctum');
Route::delete('/items/{id}', [items::class, 'del_item'])->middleware('auth:sanctum');
