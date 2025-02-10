<?php
namespace App\Http\Controllers;

use App\Models\Produtos;
use App\Models\ItemCarrinho;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function adicionarCarrinho(Request $request)
    {
        // Validação do produto e recebendo os dados
        $produtoId = $request->produto_id;
        $quantidade = $request->quantidade;
        $produto = Produtos::find($produtoId);

        // Verifica se o produto existe
        if (!$produto) {
            return redirect()->route('catalogo.index')->with('error', 'Produto não encontrado!');
        }

        // Adiciona o item ao carrinho do usuário
        ItemCarrinho::create([
            // 'usuario_id' => auth()->id(),  // Usando o ID do usuário logado
            'produto_id' => $produto->id,
            'quantidade' => $quantidade,
        ]);

        // Redireciona de volta para a página de detalhes com uma mensagem de sucesso
        return redirect()->route('catalogo.show', $produtoId)->with('success', 'Produto adicionado ao carrinho!');
    }

    public function exibirCarrinho()
    {
        // Recupera os itens do carrinho do usuário logado
        $itensCarrinho = ItemCarrinho::where('usuario_id', auth()->id())->get();

        return view('catalogo.carrinho', compact('itensCarrinho'));
    }

    public function show($id)
    {
        // Buscar o produto pelo ID
        $produto = Produtos::findOrFail($id);

        // Retornar a view com os dados do produto
        return view('catalogo.show', compact('produto'));
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $produtos = Produtos::when($search, function ($query, $search) {
            return $query->where('nome', 'like', "%{$search}%");
        })->get();

        return view('catalogo.index', compact('produtos'));
    }

}
