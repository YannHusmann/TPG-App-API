<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notification;
use App\Notifications\ResetPasswordNotification;


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
        'use_role',
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

    public function getRoleAttribute()
    {
        return $this->use_role;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function getEmailAttribute()
    {
        return $this->use_email;
    }

    public function routeNotificationForMail()
    {
        return $this->use_email;
    }

    public function getEmailForPasswordReset()
    {
        return $this->use_email;
    }   

}
