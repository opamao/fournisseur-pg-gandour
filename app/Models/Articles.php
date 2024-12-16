<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articles extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_article',
        'cls',
        'designation',
    ];

    protected $primaryKey = 'id';

    protected $table = 'articles';

    // Article.php (Modèle)
    public function stock()
    {
        return $this->hasOne(Stocks::class, 'code_stock', 'code_article');
    }
}
