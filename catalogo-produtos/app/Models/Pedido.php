<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome_cliente',
        'tipo_entrega', // O campo deve estar aqui para permitir a atribuição em massa
        'data_entrega',
        'observacao',
        'rua',
        'bairro',
        'numero',
        'quadra',
        'lote',
        'numero_contato',
        'itens_pedido',
    ];
    
    protected $casts = [
        'itens_pedido' => 'array',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
