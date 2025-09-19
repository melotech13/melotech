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
 * @property string|null $email_verification_code
 * @property \Carbon\Carbon|null $email_verification_code_expires_at
 * @property string|null $used_verification_code
 * @property \Carbon\Carbon|null $verification_completed_at
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
        'email_verified_at',
        'email_verification_code',
        'email_verification_code_expires_at',
        'used_verification_code',
        'verification_completed_at',
    ];

    /**
     * Set the user's password.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = $value;
    }

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
            'email_verification_code_expires_at' => 'datetime',
            'verification_completed_at' => 'datetime',
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

    /**
     * Check if the user's email is verified.
     *
     * @return bool
     */
    public function isEmailVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Check if the verification code is valid and not expired.
     *
     * @param string $code
     * @return bool
     */
    public function isVerificationCodeValid(string $code): bool
    {
        return $this->email_verification_code === $code 
            && $this->email_verification_code_expires_at 
            && $this->email_verification_code_expires_at->isFuture();
    }

    /**
     * Generate and set a new verification code.
     *
     * @return string
     */
    public function generateVerificationCode(): string
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $this->update([
            'email_verification_code' => $code,
            'email_verification_code_expires_at' => now()->addMinutes(15), // Code expires in 15 minutes
        ]);

        return $code;
    }

    /**
     * Mark the user's email as verified.
     * Keeps the verification code for evidence purposes.
     *
     * @return bool
     */
    public function markEmailAsVerified(): bool
    {
        return $this->update([
            'email_verified_at' => now(),
            // Keep email_verification_code for evidence
            // Keep email_verification_code_expires_at for evidence
        ]);
    }


    /**
     * Check if the stored password is hashed (bcrypt format).
     *
     * @return bool
     */
    public function isPasswordHashed(): bool
    {
        return preg_match('/^\$2[ayb]\$.{56}$/', $this->password);
    }

    /**
     * Get the display password for admin panel.
     * Returns plain text if available, otherwise indicates it's hashed.
     *
     * @return string
     */
    public function getDisplayPassword(): string
    {
        if ($this->isPasswordHashed()) {
            return '[HASHED - Click to convert]';
        }
        return $this->password;
    }

    /**
     * Convert hashed password to plain text.
     * This should only be used for admin management purposes.
     *
     * @param string $newPlainPassword
     * @return bool
     */
    public function convertToPlainText(string $newPlainPassword): bool
    {
        if (!$this->isPasswordHashed()) {
            return false; // Already plain text
        }
        
        return $this->update(['password' => $newPlainPassword]);
    }

    /**
     * Verify the given password against the user's password.
     * Since we use plain text passwords, we do a simple string comparison.
     *
     * @param string $password
     * @return bool
     */
    public function verifyPassword(string $password): bool
    {
        return $this->password === $password;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }
}
