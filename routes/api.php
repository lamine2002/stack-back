<?php

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('questions', QuestionController::class);
Route::apiResource('answers', AnswerController::class);
Route::put('answers/{answer}/validate', [AnswerController::class, 'is_validated']);
Route::put('answers/{answer}/incrementvote', [AnswerController::class, 'incrementVote']);
Route::put('answers/{answer}/decrementvote', [AnswerController::class, 'decrementVote']);
Route::apiResource('tags', TagController::class);
Route::apiResource('users', UserController::class);


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
