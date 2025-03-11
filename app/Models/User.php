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

    protected $primaryKey = 'use_id'; // Clé primaire personnalisée

    public $incrementing = true; // La clé primaire est auto-incrémentée
    protected $keyType = 'int'; // Type de la clé

    public $timestamps = true; // Active les timestamps (created_at, updated_at)

    protected $fillable = [
        'use_username',
        'use_email',
        'use_password',
    ];

    protected $hidden = [
        'use_password',
        'use_id',
    ];

    // Indique à Laravel d'utiliser la bonne colonne pour l'authentification
    public function getAuthPassword()
    {
        return $this->use_password;
    }
}
