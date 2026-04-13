<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoItemConfiguracao extends Model
{
    use HasFactory;

    protected $table = 'pedido_item_configuracoes';

    protected $fillable = [
        'pedido_item_id',
        'produto_opcao_id',
        'produto_configuracao_id',
        'quantidade',
    ];

    public function pedidoItem()
    {
        return $this->belongsTo(PedidoItem::class, 'pedido_item_id');
    }

    public function produtoOpcao()
    {
        return $this->belongsTo(ProdutoOpcao::class, 'produto_opcao_id');
    }

    public function produtoConfiguracao()
    {
        return $this->belongsTo(ProdutoConfiguracao::class, 'produto_configuracao_id');
    }
}
