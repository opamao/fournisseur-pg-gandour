<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Clients extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'username',
        'code_client',
        'nom_client',
        'email_client',
        'division_id',
    ];

    protected $primaryKey = 'id';

    protected $table = 'clients';

    protected $hidden = [
        'password_client',
    ];

    public function getAuthPassword()
    {
        return $this->password_client;
    }

    /**
     * Récupère le nom de l'identifiant utilisé par l'authentification.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id'; // ou si vous utilisez un autre champ pour l'identifiant, ajustez ici
    }

    /**
     * Récupère l'identifiant de l'utilisateur.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();  // Par défaut, renvoie la valeur de la colonne 'id'
    }
}
