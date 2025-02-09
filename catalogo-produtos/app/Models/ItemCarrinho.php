<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCarrinho extends Model
{
    use HasFactory;

    // Nome da tabela no banco de dados
    protected $table = 'carrinho_itens'; // Nome correto da tabela

    protected $fillable = ['produto_id', 'quantidade', 'preco'];
}
