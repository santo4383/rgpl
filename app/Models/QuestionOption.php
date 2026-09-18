<?php

namespace App\Models;

use App\Casts\EncryptedVaultText;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    use HasFactory;

    protected $fillable = ['question_id', 'option_key', 'option_text', 'option_html', 'is_correct', 'sort_order'];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'option_text' => EncryptedVaultText::class,
            'option_html' => EncryptedVaultText::class,
        ];
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
