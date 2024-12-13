<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articles extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_article',
        'code_article',
        'cls_article',
        'description_article',
    ];

    protected $primaryKey = 'id';

    protected $table = 'articles';
}
