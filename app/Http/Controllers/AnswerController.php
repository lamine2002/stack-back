<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnswerRequest;
use App\Http\Requests\AnswerVoteRequest;
use App\Http\Requests\ValidateAnswerRequest;
use App\Models\Answer;
use App\Models\AnswerValidation;
use App\Models\AnswerVote;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AnswerRequest $request)
    {
        try {
            $answer = Answer::create($request->validated());
            return response()->json([
                'answer' => $answer,
                'message' => 'Reponse ajoutee avec succes',
                'status' => 201
            ], 201);
        }catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de l\'ajout de la reponse',
                'status' => 500
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        /*try {
            $answers = $question->answers;
            return response()->json([
                'answers' => $answers,
                'message' => 'Reponses recuperees avec succes',
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la recuperation des reponses',
                'status' => 500
            ], 500);
        }*/
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $answer = Answer::find($id);
            return response()->json([
                'answer' => $answer,
                'message' => 'Reponse recuperee avec succes',
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la recuperation de la reponse',
                'status' => 500
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AnswerRequest $request, string $id)
    {
        try {
            $answer = Answer::find($id);
            $answer->update($request->validated());
            return response()->json([
                'answer' => $answer,
                'message' => 'Reponse modifiee avec succes',
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la modification de la reponse',
                'status' => 500
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $answer = Answer::find($id);
            $answer->delete();
            return response()->json([
                'message' => 'Reponse supprimee avec succes',
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la suppression de la reponse',
                'status' => 500
            ], 500);
        }
    }

    /*public function is_validated(ValidateAnswerRequest $request, Answer $answer)
    {
        try {
            if (User::where('id', $request->supervisor_id)->first()->role !== 'supervisor') {
                return response()->json([
                    'message' => 'Vous n\'etes pas un superviseur',
                    'status' => 403
                ], 403);
            }
            $answer->is_validated = true;
            $answer->save();
            $answerValidation = AnswerValidation::create([
                'answer_id' => $answer->id,
                'supervisor_id' => $request->validated('supervisor_id')
            ]);
            return response()->json([
                'answer' => $answer,
                'answer_validation' => $answerValidation,
                'message' => 'Reponse validee avec succes',
                'status' => 200
            ], 200);
        }catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la validation de la reponse',
                'status' => 500
            ], 500);
        }

    }*/

    public function incrementVote(Answer $answer)
    {
        try {
            // Recuperer l'utilisateur qui a vote
            $user = Auth()->user();
            // Verifier si l'utilisateur est un superviseur
            if ($user->role !== 'supervisor') {
                return response()->json([
                    'message' => 'Il faut avoir 10 de reputation pour voter pour une reponse',
                    'status' => 403
                ], 403);
            }
//            return $user;
            // Verifier si l'utilisateur a deja vote pour cette reponse
            if ($answerVote = AnswerVote::where('user_id', $user->id)->where('answer_id', $answer->id)->exists()) {
                // Verifier si l'utilisateur a deja vote pour cette reponse en decrementant le vote
                if ($answerVote->decrement_vote) {
                    $answerVote->decrement_vote = false;
                    $answerVote->increment_vote = false;
                    $answerVote->save();
                    $answer->increment('votes');
                    return response()->json([
                        'answer' => $answer,
                        'message' => 'Vote ajoute avec succes',
                        'status' => 200
                    ], 200);
                } else if (!$answerVote->increment_vote) {
                    $answerVote->increment_vote = true;
                    $answerVote->save();
                    $answer->increment('votes');
                    return response()->json([
                        'answer' => $answer,
                        'message' => 'Vote ajoute avec succes',
                        'status' => 200
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'Vous avez deja vote pour cette reponse',
                        'status' => 403
                    ], 403);
                }
            }
            // Ajouter l'utilisateur a la liste des votants pour cette reponse
            AnswerVote::create([
                'user_id' => $user->id,
                'answer_id' => $answer->id
            ]);
            $answer->increment('votes');
            return response()->json([
                'answer' => $answer,
                'message' => 'Vote ajoute avec succes',
                'status' => 200
            ], 200);
        }catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de l\'ajout du vote',
                'status' => 500
            ], 500);
        }
    }

    public function decrementVote(Answer $answer)
    {
        try {
            // Recuperer l'utilisateur qui a vote
            $user = Auth()->user();
            // Verifier si l'utilisateur est un superviseur
            if ($user->role !== 'supervisor') {
                return response()->json([
                    'message' => 'Il faut avoir 10 de reputation pour voter pour une reponse',
                    'status' => 403
                ], 403);
            }
            // Verifier si l'utilisateur a deja vote pour cette reponse
            if ($answerVote = AnswerVote::where('user_id', $user->id)->where('answer_id', $answer->id)->exists()) {
                // Verifier si l'utilisateur a deja vote pour cette reponse en incrementant le vote
                if ($answerVote->increment_vote) {
                    $answerVote->increment_vote = false;
                    $answerVote->decrement_vote = false;
                    $answerVote->save();
                    $answer->decrement('votes');
                    return response()->json([
                        'answer' => $answer,
                        'message' => 'Vote retire avec succes',
                        'status' => 200
                    ], 200);
                } else if (!$answerVote->decrement_vote) {
                    $answerVote->decrement_vote = true;
                    $answerVote->save();
                    $answer->decrement('votes');
                    return response()->json([
                        'answer' => $answer,
                        'message' => 'Vote retire avec succes',
                        'status' => 200
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'Vous avez deja vote pour cette reponse',
                        'status' => 403
                    ], 403);
                }
            }
            // Ajouter l'utilisateur a la liste des votants pour cette reponse
            $answerVote = AnswerVote::create([
                'user_id' => $user->id,
                'answer_id' => $answer->id
            ]);
            $answer->decrement('votes');
            return response()->json([
                'answer' => $answer,
                'answer_vote' => $answerVote,
                'message' => 'Vote retire avec succes',
                'status' => 200
            ], 200);
        }catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la suppression du vote',
                'status' => 500
            ], 500);
        }
    }
}
