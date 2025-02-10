<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias'; // Se o nome da tabela for diferente de 'categorias'

    public function produtos()
    {
        return $this->hasMany(Produtos::class, 'categoria_id'); 
        // Substitua 'categoria_id' pelo nome correto da chave estrangeira na tabela 'produtos'
    }
}
