<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Farm;
use App\Models\PhotoAnalysis;
use App\Models\CropProgressUpdate;
use App\Models\Notification;

/**
 * Class User
 * 
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string|null $phone
 * @property \Carbon\Carbon|null $email_verified_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Farm[] $farms
 * 
 * @method \Illuminate\Database\Eloquent\Relations\HasMany farms()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method bool update(array $attributes = [], array $options = [])
 * @method static \App\Models\User create(array $attributes = [])
 * @method static \App\Models\User find($id, $columns = ['*'])
 * @method static \App\Models\User findOrFail($id, $columns = ['*'])
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
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
            // Removed 'password' => 'hashed' to allow plain text passwords
        ];
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a regular user.
     *
     * @return bool
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Get the farms for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function farms(): HasMany
    {
        return $this->hasMany(Farm::class);
    }

    /**
     * Get the photo analyses for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photoAnalyses(): HasMany
    {
        return $this->hasMany(PhotoAnalysis::class);
    }

    /**
     * Get the crop progress updates for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cropProgressUpdates(): HasMany
    {
        return $this->hasMany(CropProgressUpdate::class);
    }

    /**
     * Get the notifications for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}
