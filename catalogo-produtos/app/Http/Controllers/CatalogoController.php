<?php

namespace App\Http\Controllers;

use App\Models\Produtos;
use App\Models\ItemCarrinho;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function adicionarCarrinho(Request $request)
    {
        // Validação dos dados recebidos
        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|integer|min:1',
        ]);

        $produtoId = $request->produto_id;
        $quantidade = $request->quantidade;
        $produto = Produtos::findOrFail($produtoId);

        // Verifica se o usuário está autenticado
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado para adicionar ao carrinho.');
        }

        // Adiciona o item ao carrinho do usuário autenticado
        ItemCarrinho::create([
            'usuario_id' => auth()->id(),
            'produto_id' => $produto->id,
            'quantidade' => $quantidade,
        ]);

        return redirect()->route('catalogo.show', $produtoId)->with('success', 'Produto adicionado ao carrinho!');
    }

    public function exibirCarrinho()
    {
        // Recupera os itens do carrinho do usuário logado
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Faça login para ver seu carrinho.');
        }

        $itensCarrinho = ItemCarrinho::where('usuario_id', auth()->id())->with('produto')->get();

        return view('catalogo.carrinho', compact('itensCarrinho'));
    }

    public function show($id)
    {
        // Buscar o produto pelo ID
        $produto = Produtos::with('categoria')->findOrFail($id);

        return view('catalogo.show', compact('produto'));
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        // Obtendo todas as categorias com seus produtos
        $categorias = Categoria::with(['produtos' => function ($query) use ($search) {
            if ($search) {
                $query->where('nome', 'like', "%{$search}%");
            }
        }])->get();

        return view('catalogo.index', compact('categorias', 'search'));
    }
}
