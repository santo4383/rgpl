<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionImage extends Model
{
    use HasFactory;

    protected $fillable = ['question_id', 'file_path', 'file_name', 'mime_type', 'file_size', 'sort_order'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
