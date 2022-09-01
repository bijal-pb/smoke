<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Follower;
use App\Models\Post;
use App\Models\Country;

use Auth;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function country()
    {
        return $this->hasOne(Country::class,'id','country_id');
    }

    public function following()
    {
        return $this->hasMany(Follower::class, 'follow_by');
    }

    public function follower()
    {
        return $this->hasMany(Follower::class, 'follow_to');
    }
    
    public function posts()
    {
        return $this->hasMany(Post::class, 'post_by')->with(['likes','flavour_category','flavour','reviews'])->withCount('likes');
    }

    public function getPhotoAttribute($value)
    {
        if ($value) {
            return asset('/user/' . $value);
        } else {
            return null;
        }
    }
}
