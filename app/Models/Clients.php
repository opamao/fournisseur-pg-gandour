<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Clients extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
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
}
