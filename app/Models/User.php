<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\Image\UserProfileImage;
use App\Service\Contracts\Model\CanUploadImage;
use App\Service\Traits\HasImage;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $imageUrlColumn = 'profile_image_url';
    protected $format         = 'webp';
    protected $disk           = 'public';
    protected $dirPath        = 'upload/images/profile/user/';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image_url'
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function profileImageUrl(): Attribute
    {
        return new Attribute(
            get: function ($value = null) {
                if ($value == null) {
                    return null;
                }
                return  config('app.frontend_url') . 'storage/' . $value;
            }
        );
    }

    public function resetPassword():HasOne
    {
        return $this->hasOne(UserResetPassword::class);
    }

    public function verifyEmail():HasOne
    {
        return $this->hasOne(UserVerifyEmail::class);
    }
}
