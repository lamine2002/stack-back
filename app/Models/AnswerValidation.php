<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswerValidation extends Model
{
    use HasFactory;
    protected $table = 'answer_validations';

    protected $guarded = [];

    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class);
    }
}
