<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCarrinho extends Model
{
    use HasFactory;

    // Defina os campos que podem ser preenchidos em massa
    protected $fillable = ['usuario_id', 'produto_id', 'quantidade'];

    protected $table = 'carrinho_itens';

    public function produto()
    {
        return $this->belongsTo(Produtos::class);
    }
}

