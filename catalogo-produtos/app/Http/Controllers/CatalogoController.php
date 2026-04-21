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
        $categorias = Categoria::where('ativo', true)->with(['produtos' => function ($query) use ($search) {
            $query->where('ativo', true);
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

    public function editarConfiguracao($id)
    {
        $carrinho = session()->get('carrinho', []);

        if (!isset($carrinho[$id])) {
            return redirect()->route('catalogo.carrinho')->with('error', 'Item não encontrado no carrinho.');
        }

        $item = $carrinho[$id];
        $produto = Produtos::with(['opcoes.configuracoes'])->findOrFail($item['id']);

        return view('catalogo.editar-configuracao', compact('produto', 'item', 'id'));
    }

    public function atualizarConfiguracao(Request $request, $id)
    {
        $carrinho = session()->get('carrinho', []);

        if (!isset($carrinho[$id])) {
            return redirect()->route('catalogo.carrinho')->with('error', 'Item não encontrado no carrinho.');
        }

        $item = $carrinho[$id];
        $produto = Produtos::with('opcoes')->findOrFail($item['id']);

        $validator = Validator::make($request->all(), [
            'quantidade' => 'required|integer|min:1',
            'opcoes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $quantidade = $request->quantidade;
        $opcoesRecebidas = $request->opcoes ?? [];

        // Recalcular preço adicional
        $precoAdicional = 0;
        $errors = [];

        foreach ($produto->opcoes as $opcao) {
            if ($opcao->tipo == 'selecao_unica') {
                $configIds = isset($opcoesRecebidas[$opcao->id]) && !empty($opcoesRecebidas[$opcao->id]) ? [$opcoesRecebidas[$opcao->id]] : [];
            } elseif ($opcao->tipo == 'selecao_multipla') {
                $configIds = $opcoesRecebidas[$opcao->id] ?? [];
            } else {
                $configIds = []; // quantidade_fixa não tem input do usuário
            }

            if ($opcao->obrigatorio && empty($configIds)) {
                $errors[] = "A opção '{$opcao->nome}' é obrigatória.";
                continue;
            }

            if ($opcao->tipo == 'selecao_multipla' && $opcao->max_selecoes > 0 && count($configIds) > $opcao->max_selecoes) {
                $errors[] = "A opção '{$opcao->nome}' permite no máximo {$opcao->max_selecoes} seleções.";
                continue;
            }

            foreach ($configIds as $configId) {
                $configuracao = $opcao->configuracoes->find($configId);
                if ($configuracao) {
                    $precoAdicional += $configuracao->preco_adicional;
                }
            }
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        // Atualizar o item no carrinho
        $carrinho[$id]['quantidade'] = $quantidade;
        $carrinho[$id]['opcoes'] = $opcoesRecebidas;
        $carrinho[$id]['preco_adicional'] = $precoAdicional;
        $carrinho[$id]['preco_total'] = ($produto->preco + $precoAdicional) * $quantidade;

        session()->put('carrinho', $carrinho);

        return redirect()->route('pedido.formulario')->with('success', 'Configuração do item atualizada!');
    }
}