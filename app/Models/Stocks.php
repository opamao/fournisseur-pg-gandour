<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stocks extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_stock',
        'quantite_initiale',
        'client_id',
    ];

    protected $primaryKey = 'id';

    protected $table = 'stocks';

    public function client()
    {
        return $this->belongsTo(Clients::class);
    }
}
