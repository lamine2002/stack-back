<?php

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\SecureRoute;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
//    $user = $request->user();
//    $number_of_validated_answers = Answer::where('user_id', $user->id)->where('is_validated', true)->count();
    return $request->user();
})->middleware(['auth:sanctum', SecureRoute::class]);

Route::middleware(['auth:sanctum', SecureRoute::class])->group(function () {
    Route::apiResource('questions', QuestionController::class)->except(['index', 'show']);
    Route::apiResource('answers', AnswerController::class)->except(['index', 'show']);
    Route::put('answers/{answer}/incrementvote', [AnswerController::class, 'incrementVote']);
    Route::put('answers/{answer}/decrementvote', [AnswerController::class, 'decrementVote']);
    Route::put('answers/{answer}/validate', [AnswerController::class, 'is_validated']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('tags', TagController::class)->except(['index']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});

Route::withoutMiddleware([SecureRoute::class])->group(function () {
    Route::apiResource('questions', QuestionController::class)->only(['index', 'show']);
    Route::apiResource('answers', AnswerController::class)->only(['index', 'show']);
    Route::apiResource('tags', TagController::class)->only(['index']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});
/*Route::apiResource('questions', QuestionController::class)->only(['index', 'show']);
Route::apiResource('answers', AnswerController::class)->only(['index', 'show']);
Route::apiResource('tags', TagController::class);



Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);*/
