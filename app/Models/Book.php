<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Book extends Authenticatable
{
    use Notifiable, HasApiTokens;
    protected $fillable = ['name', 'description', 'category', 'recomended_age', 'ISBN'];

    function author()
    {
        return $this->belongsTo(User::class);
    }
}
