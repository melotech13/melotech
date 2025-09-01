<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Farm;
use App\Models\PhotoAnalysis;
use App\Models\CropProgressUpdate;

/**
 * Class User
 * 
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
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
        'phone',
    ];

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
        return $this->hasMany(\App\Models\PhotoAnalysis::class);
    }

    /**
     * Get the crop progress updates for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cropProgressUpdates(): HasMany
    {
        return $this->hasMany(\App\Models\CropProgressUpdate::class);
    }
}
