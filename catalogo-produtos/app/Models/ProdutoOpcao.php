<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoOpcao extends Model
{
    use HasFactory;

    protected $table = 'produto_opcoes';

    protected $fillable = [
        'produto_id',
        'categoria',
        'nome',
        'tipo',
        'quantidade_minima',
        'quantidade_maxima',
        'obrigatorio',
        'ordem',
    ];

    public function produto()
    {
        return $this->belongsTo(Produtos::class, 'produto_id');
    }

    public function configuracoes()
    {
        return $this->hasMany(ProdutoConfiguracao::class, 'produto_opcao_id');
    }
}
