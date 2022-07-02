<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'access_token',
        'email_verified_at',
        'password',
        'follow_person',
        'follow_page'
    ];

    public function page()
    {
        return $this->hasMany(Page::class, 'created_by');
    }
    public function postByPage()
    {
        return $this->hasMany(Post::class, 'post_by')->where('page_id', '!=', '0');
    }
    public function singlePost()
    {
        return $this->hasMany(Post::class, 'post_by')->where('page_id', '0');
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
