<?php

namespace App\Http\Controllers;

use App\Models\Produtos;
use Illuminate\Http\Request;

class ProdutosController extends Controller
{
    public function create()
    {
        return view('produtos.produtos_create'); // Exibe o formulário de cadastro
    }

    public function storeProdutos(Request $request)
    {
        $produtos = new Produtos;

        $produtos->nome = $request->nome;
        $produtos->descricao = $request->descricao;
        $produtos->preco = $request->preco;

        $produtos->save();

        return redirect()->route('produtos.create')->with('msg', 'Produto cadastrado com sucesso!');
    }
}


