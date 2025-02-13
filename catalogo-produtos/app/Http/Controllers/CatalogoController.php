<?php

namespace App\Http\Controllers;

use App\Models\Produtos;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function adicionarCarrinho(Request $request)
    {
        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|integer|min:1',
        ]);

        $produtoId = $request->produto_id;
        $quantidade = $request->quantidade;
        $produto = Produtos::findOrFail($produtoId);

        // Obtém o carrinho atual da sessão
        $carrinho = session()->get('carrinho', []);

        // Verifica se o produto já está no carrinho
        if (isset($carrinho[$produtoId])) {
            $carrinho[$produtoId]['quantidade'] += $quantidade;
        } else {
            // Adiciona um novo produto ao carrinho
            $carrinho[$produtoId] = [
                'id' => $produto->id,
                'nome' => $produto->nome,
                'preco' => $produto->preco,
                'imagem' => $produto->imagem,
                'quantidade' => $quantidade
            ];
        }

        // Atualiza o carrinho na sessão
        session()->put('carrinho', $carrinho);

        return redirect()->route('catalogo.show', $produtoId)->with('success', 'Produto adicionado ao carrinho!');
    }


    public function exibirCarrinho()
    {
        // Obtém os itens do carrinho da sessão (se existirem)
        $itensCarrinho = session()->get('carrinho', []);

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

    public function atualizarCarrinho(Request $request, $id)
    {
        $request->validate([
            'quantidade' => 'required|integer|min:1',
        ]);

        $carrinho = session()->get('carrinho', []);

        if (isset($carrinho[$id])) {
            $carrinho[$id]['quantidade'] = $request->quantidade;
            session()->put('carrinho', $carrinho);
        }

        return back()->with('success', 'Quantidade do produto atualizada!');
    }

    public function removerDoCarrinho($id)
    {
        $carrinho = session()->get('carrinho', []);
    
        if (isset($carrinho[$id])) {
            unset($carrinho[$id]);
            session()->put('carrinho', $carrinho);
        }
    
        return back()->with('success', 'Item removido do carrinho!');
    }
    
}
