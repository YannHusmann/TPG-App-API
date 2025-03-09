<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users'; // Nom de la table

    protected $primaryKey = 'use_id'; // Clé primaire 

    public $timestamps = true; // Active les timestamps 

    protected $fillable = [
        'use_username',
        'use_email',
        'use_password',
    ];

    protected $hidden = [
        'use_password',
    ];


    public function getAuthPassword()
    {
        return $this->use_password;
    }
}
