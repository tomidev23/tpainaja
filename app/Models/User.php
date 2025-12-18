<?php
namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
    'name',
    'email',
    'password',
    'role',
];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the exams created by the user.
     */
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'user_id'); // Make sure the foreign key is correct
    }

    /**
     * Get the exam results for the user.
     */
    public function hasilTes(): HasMany
    {
        return $this->hasMany(HasilTes::class);
    }

    public function examResults()
{
    return $this->hasMany(ExamResult::class);
}

    // User model (app/Models/User.php)
public function delete()
{
    return parent::delete(); // This calls the parent delete method (usually unnecessary)
}
    
}
