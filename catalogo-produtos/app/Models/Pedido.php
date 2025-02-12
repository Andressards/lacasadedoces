<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id', 'nome_cliente', 'data_entrega', 'itens_pedido'
    ];

    protected $casts = [
        'itens_pedido' => 'array',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
