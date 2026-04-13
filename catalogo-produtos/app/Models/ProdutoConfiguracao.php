<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoConfiguracao extends Model
{
    use HasFactory;

    protected $table = 'produto_configuracoes';

    protected $fillable = [
        'produto_opcao_id',
        'valor',
        'descricao',
        'preco_adicional',
        'quantidade_disponivel',
        'ativo',
        'ordem',
    ];

    protected $casts = [
        'preco_adicional' => 'decimal:2',
    ];

    public function produtoOpcao()
    {
        return $this->belongsTo(ProdutoOpcao::class, 'produto_opcao_id');
    }
}
