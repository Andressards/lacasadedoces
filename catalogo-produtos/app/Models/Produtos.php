<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produtos extends Model
{
    use HasFactory;

    protected $table = 'produtos';
    
    protected $fillable = ['nome', 'descricao', 'preco', 'status'];

    // Em Produto.php
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

}