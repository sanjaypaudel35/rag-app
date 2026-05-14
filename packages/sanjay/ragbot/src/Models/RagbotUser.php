<?php

namespace Sanjay\Ragbot\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Sanjay\Ragbot\Database\Factories\RagbotUserFactory;
use Sanjay\Ragbot\Models\Traits\BelongsToProject;
use Sanjay\Ragbot\Notifications\ResetPassword;
use Sanjay\Ragbot\Notifications\VerifyEmail;

/**
 * Model representing a tenant-specific user.
 *
 * @property string $id
 * @property string $project_id
 * @property string $firstname
 * @property string $lastname
 * @property string $name
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 */
class RagbotUser extends Authenticatable implements MustVerifyEmail
{
    use BelongsToProject;
    use HasFactory;
    use HasUuids, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ragbot_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'firstname',
        'lastname',
        'name',
        'email',
        'password',
        'profile_photo_path',
    ];

    /**
     * Get the user's full name.
     */
    public function getNameAttribute($value): string
    {
        if ($this->firstname && $this->lastname) {
            return $this->firstname.' '.$this->lastname;
        }

        return $value ?? '';
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail($this->project));
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token, $this->project));
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return RagbotUserFactory::new();
    }
}
