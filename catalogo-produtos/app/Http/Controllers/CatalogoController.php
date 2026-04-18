<?php

namespace App\Http\Controllers;

use App\Models\Produtos;
use App\Models\Categoria;
use App\Models\ProdutoOpcao;
use App\Models\ProdutoConfiguracao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CatalogoController extends Controller
{
    public function adicionarCarrinho(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|integer|min:1',
            'opcoes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $produtoId = $request->produto_id;
        $quantidade = $request->quantidade;
        $opcoesRecebidas = $request->opcoes ?? [];
        $produto = Produtos::with('opcoes')->findOrFail($produtoId);

        $errors = [];

        // Validação de Opções Obrigatórias e Limites
        foreach ($produto->opcoes as $opcao) {
            $selecionados = isset($opcoesRecebidas[$opcao->id]) ? $opcoesRecebidas[$opcao->id] : [];
            
            // Converter para array se for seleção única (string/int)
            if (!is_array($selecionados)) {
                $selecionados = $selecionados ? [$selecionados] : [];
            }

            $totalSelecionados = count($selecionados);

            // Validar obrigatoriedade
            if ($opcao->obrigatorio && $totalSelecionados === 0) {
                $errors["opcoes.{$opcao->id}"] = "A opção '{$opcao->nome}' é obrigatória.";
            }

            // Validar limites para seleção múltipla
            if ($opcao->tipo === 'selecao_multipla') {
                if ($totalSelecionados < $opcao->quantidade_minima) {
                    $errors["opcoes.{$opcao->id}"] = "A opção '{$opcao->nome}' requer no mínimo {$opcao->quantidade_minima} seleções.";
                } elseif ($totalSelecionados > $opcao->quantidade_maxima) {
                    $errors["opcoes.{$opcao->id}"] = "A opção '{$opcao->nome}' permite no máximo {$opcao->quantidade_maxima} seleções.";
                }
            }
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        // Calcular preço adicional das opções
        $precoAdicional = 0;
        if (!empty($opcoesRecebidas)) {
            foreach ($opcoesRecebidas as $opcaoId => $configIds) {
                $configIds = is_array($configIds) ? $configIds : [$configIds];
                foreach ($configIds as $configId) {
                    $configuracao = ProdutoConfiguracao::find($configId);
                    if ($configuracao) {
                        $precoAdicional += $configuracao->preco_adicional;
                    }
                }
            }
        }

        // Obtém o carrinho atual da sessão
        $carrinho = session()->get('carrinho', []);

        // Criar identificador único para item com configurações
        // Usamos ksort para garantir que a ordem das opções não mude o hash
        $opcoesParaHash = $opcoesRecebidas;
        ksort($opcoesParaHash);
        $itemKey = $produtoId . '_' . md5(serialize($opcoesParaHash));

        // Adiciona ou atualiza o produto no carrinho
        $carrinho[$itemKey] = [
            'id' => $produto->id,
            'nome' => $produto->nome,
            'preco' => $produto->preco,
            'preco_adicional' => $precoAdicional,
            'imagem' => $produto->imagem,
            'quantidade' => $quantidade,
            'opcoes' => $opcoesRecebidas,
            'preco_total' => ($produto->preco + $precoAdicional) * $quantidade
        ];

        // Atualiza o carrinho na sessão
        session()->put('carrinho', $carrinho);

        return redirect()->route('catalogo.carrinho')->with('success', 'Produto adicionado ao carrinho!');
    }

    public function exibirCarrinho()
    {
        $itensCarrinho = session()->get('carrinho', []);
        return view('catalogo.carrinho', compact('itensCarrinho'));
    }

    public function show($id)
    {
        $produto = Produtos::with(['categoria', 'opcoes.configuracoes'])->findOrFail($id);
        return view('catalogo.show', compact('produto'));
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
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