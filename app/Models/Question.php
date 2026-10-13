<?php

namespace App\Models;

use App\Casts\EncryptedVaultText;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id', 'category_id', 'subcategory_id', 'question_number', 'question_code',
        'question_type', 'language',
        'question_text', 'question_html', 'explanation_text', 'explanation_html',
        'difficulty', 'source_type', 'source_name', 'source_year', 'source_reference',
        'status', 'is_free', 'is_premium', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'question_text' => EncryptedVaultText::class,
            'question_html' => EncryptedVaultText::class,
            'explanation_text' => EncryptedVaultText::class,
            'explanation_html' => EncryptedVaultText::class,
            'is_free' => 'boolean',
            'is_premium' => 'boolean',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function images()
    {
        return $this->hasMany(QuestionImage::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function practiceAttempts()
    {
        return $this->hasMany(PracticeAttempt::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function isBookmarkedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->bookmarks()->where('user_id', $user->id)->exists();
    }
}
