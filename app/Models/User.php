<?php

namespace App\Models;

use App\Notifications\SendVerifyWithQueueNotification;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, CascadeSoftDeletes;
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
        'email_verified_at' // for testing purpose
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
    ];
    protected $cascadeDeletes = ['posts'];
    protected $guarded = false;

    const ROLE_ADMIN = 0;
    const ROLE_READER = 1;

    public static function getRoles() {
        return [
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_READER => 'Reader',
        ];
    }

    public function comments()
    {
        return $this->hasMany(Comment::class,'user_id','id');
    }

    public function likedPosts()
    {
        return $this->belongsToMany(Post::class,'post_user_likes','user_id','post_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'author_id','id');
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new SendVerifyWithQueueNotification());
    }

}
