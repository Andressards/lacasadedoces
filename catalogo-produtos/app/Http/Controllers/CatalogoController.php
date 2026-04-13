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
            'opcoes' => 'nullable|array',
        ]);

        $produtoId = $request->produto_id;
        $quantidade = $request->quantidade;
        $opcoes = $request->opcoes ?? [];
        $produto = Produtos::findOrFail($produtoId);

        // Obtém o carrinho atual da sessão
        $carrinho = session()->get('carrinho', []);

        // Calcular preço adicional das opções
        $precoAdicional = 0;
        if (!empty($opcoes)) {
            foreach ($opcoes as $opcaoId => $configIds) {
                $configIds = is_array($configIds) ? $configIds : [$configIds];
                foreach ($configIds as $configId) {
                    $configuracao = \App\Models\ProdutoConfiguracao::find($configId);
                    if ($configuracao) {
                        $precoAdicional += $configuracao->preco_adicional;
                    }
                }
            }
        }

        // Criar identificador único para item com configurações
        $itemKey = $produtoId . '_' . md5(serialize($opcoes));

        // Adiciona o produto ao carrinho (sempre como novo item para configurações diferentes)
        $carrinho[$itemKey] = [
            'id' => $produto->id,
            'nome' => $produto->nome,
            'preco' => $produto->preco,
            'preco_adicional' => $precoAdicional,
            'imagem' => $produto->imagem,
            'quantidade' => $quantidade,
            'opcoes' => $opcoes,
            'preco_total' => ($produto->preco + $precoAdicional) * $quantidade
        ];

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
        // Buscar o produto pelo ID com suas opções e configurações
        $produto = Produtos::with(['categoria', 'opcoes.configuracoes'])->findOrFail($id);

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
            $precoUnitario = $carrinho[$id]['preco'] + ($carrinho[$id]['preco_adicional'] ?? 0);
            $carrinho[$id]['preco_total'] = $precoUnitario * $request->quantidade;
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
