<?php

namespace Sanjay\Ragbot\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Sanjay\Ragbot\Database\Factories\RagbotUserFactory;
use Sanjay\Ragbot\Models\Traits\BelongsToProject;

/**
 * Model representing a tenant-specific user.
 *
 * @property string $id
 * @property string $project_id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 */
class RagbotUser extends Authenticatable
{
    use HasUuids, Notifiable;
    use HasFactory;
    use BelongsToProject;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "ragbot_users";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "project_id",
        "name",
        "email",
        "password",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        "password",
        "remember_token",
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return RagbotUserFactory::new();
    }
}
