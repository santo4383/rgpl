<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'mobile_number',
        'dob',
        'address',
        'email',
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function hasRole($roleName)
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function hasPermission($permissionName)
    {
        // First check if user is super admin
        if ($this->hasRole('SUPER_ADMIN')) {
            return true;
        }

        // Get all user's roles
        $roles = $this->roles()->with('permissions')->get();

        foreach ($roles as $role) {
            if ($role->permissions->contains('name', $permissionName)) {
                return true;
            }
        }

        return false;
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_user');
    }

    public function practiceAttempts()
    {
        return $this->hasMany(PracticeAttempt::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function uniqueQuestionsAttemptedCount($courseId = null): int
    {
        $query = $this->practiceAttempts();
        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        return $query->count();
    }

    public function correctAttemptsCount($courseId = null): int
    {
        $query = $this->practiceAttempts()->where('is_correct', true);
        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        return $query->count();
    }

    public function accuracyPercentage($courseId = null): float
    {
        $total = $this->uniqueQuestionsAttemptedCount($courseId);
        if ($total === 0) {
            return 0.0;
        }
        $correct = $this->correctAttemptsCount($courseId);

        return round(($correct / $total) * 100, 1);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function hasActiveSubscription($courseId = null): bool
    {
        $query = $this->subscriptions()
            ->where('status', 'ACTIVE')
            ->where('expires_at', '>', now())
            ->whereHas('plan', function ($q) use ($courseId) {
                if ($courseId) {
                    $q->where(function ($sub) use ($courseId) {
                        $sub->where('is_all_courses', true)
                            ->orWhereHas('courses', function ($c) use ($courseId) {
                                $c->where('courses.id', $courseId);
                            });
                    });
                } else {
                    $q->where('is_all_courses', true);
                }
            });

        return $query->exists();
    }

    public function hasExceededFreeQuota($courseId): bool
    {
        if ($this->hasActiveSubscription($courseId)) {
            return false;
        }

        $course = Course::find($courseId);
        if (! $course) {
            return false;
        }

        $limit = $course->free_question_limit ?? 500;
        $attempted = $this->uniqueQuestionsAttemptedCount($courseId);

        return $attempted >= $limit;
    }

    public function freeQuotaRemaining($courseId): int
    {
        $course = Course::find($courseId);
        if (! $course) {
            return 0;
        }

        $limit = $course->free_question_limit ?? 500;
        $attempted = $this->uniqueQuestionsAttemptedCount($courseId);

        return max(0, $limit - $attempted);
    }

    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }
}
