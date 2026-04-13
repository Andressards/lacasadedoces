<?php

namespace App\Http\Controllers;

use App\Models\Produtos;
use App\Models\ProdutoOpcao;
use App\Models\ProdutoConfiguracao;
use Illuminate\Http\Request;

class ProdutoConfiguracaoController extends Controller
{
    public function index()
    {
        $produtos = Produtos::with('opcoes.configuracoes')->get();
        return view('configuracoes.index', compact('produtos'));
    }

    public function create($produtoId)
    {
        $produto = Produtos::findOrFail($produtoId);
        return view('configuracoes.create', compact('produto'));
    }

    public function store(Request $request, $produtoId)
    {
        $request->validate([
            'categoria' => 'required|string',
            'nome' => 'required|string',
            'tipo' => 'required|in:selecao_unica,selecao_multipla,quantidade_fixa',
            'quantidade_fixa' => 'required_if:tipo,quantidade_fixa|integer|min:1',
            'quantidade_minima' => 'required_if:tipo,selecao_multipla|integer|min:1',
            'quantidade_maxima' => 'required_if:tipo,selecao_multipla|integer|min:1',
            'obrigatorio' => 'boolean',
            'configuracoes' => 'required|array|min:1',
            'configuracoes.*.valor' => 'required|string',
        ]);

        $produto = Produtos::findOrFail($produtoId);

        $quantidadeMinima = null;
        $quantidadeMaxima = null;

        if ($request->tipo === 'selecao_multipla') {
            $quantidadeMinima = $request->quantidade_minima;
            $quantidadeMaxima = $request->quantidade_maxima;
        } elseif ($request->tipo === 'quantidade_fixa') {
            $quantidadeMinima = $request->quantidade_fixa;
            $quantidadeMaxima = $request->quantidade_fixa;
        }

        $opcao = ProdutoOpcao::create([
            'produto_id' => $produto->id,
            'categoria' => $request->categoria,
            'nome' => $request->nome,
            'tipo' => $request->tipo,
            'quantidade_minima' => $quantidadeMinima,
            'quantidade_maxima' => $quantidadeMaxima,
            'obrigatorio' => $request->obrigatorio ?? true,
            'ordem' => ProdutoOpcao::where('produto_id', $produto->id)->max('ordem') + 1,
        ]);

        foreach ($request->configuracoes as $config) {
            ProdutoConfiguracao::create([
                'produto_opcao_id' => $opcao->id,
                'valor' => $config['valor'],
                'descricao' => $config['descricao'] ?? null,
                'preco_adicional' => $config['preco_adicional'] ?? 0,
                'ordem' => $config['ordem'] ?? 0,
            ]);
        }

        return redirect()->route('produtos.configuracoes.index')->with('success', 'Configuracao criada com sucesso!');
    }

    public function edit($produtoId, $opcaoId)
    {
        $produto = Produtos::findOrFail($produtoId);
        $opcao = ProdutoOpcao::with('configuracoes')->findOrFail($opcaoId);
        return view('configuracoes.edit', compact('produto', 'opcao'));
    }

    public function update(Request $request, $produtoId, $opcaoId)
    {
        $request->validate([
            'categoria' => 'required|string',
            'nome' => 'required|string',
            'tipo' => 'required|in:selecao_unica,selecao_multipla,quantidade_fixa',
            'quantidade_fixa' => 'required_if:tipo,quantidade_fixa|integer|min:1',
            'quantidade_minima' => 'required_if:tipo,selecao_multipla|integer|min:1',
            'quantidade_maxima' => 'required_if:tipo,selecao_multipla|integer|min:1',
            'obrigatorio' => 'boolean',
            'configuracoes' => 'required|array|min:1',
            'configuracoes.*.valor' => 'required|string',
        ]);

        $opcao = ProdutoOpcao::findOrFail($opcaoId);

        $quantidadeMinima = null;
        $quantidadeMaxima = null;

        if ($request->tipo === 'selecao_multipla') {
            $quantidadeMinima = $request->quantidade_minima;
            $quantidadeMaxima = $request->quantidade_maxima;
        } elseif ($request->tipo === 'quantidade_fixa') {
            $quantidadeMinima = $request->quantidade_fixa;
            $quantidadeMaxima = $request->quantidade_fixa;
        }

        $opcao->update([
            'categoria' => $request->categoria,
            'nome' => $request->nome,
            'tipo' => $request->tipo,
            'quantidade_minima' => $quantidadeMinima,
            'quantidade_maxima' => $quantidadeMaxima,
            'obrigatorio' => $request->obrigatorio ?? true,
        ]);

        $opcao->configuracoes()->delete();

        foreach ($request->configuracoes as $config) {
            ProdutoConfiguracao::create([
                'produto_opcao_id' => $opcao->id,
                'valor' => $config['valor'],
                'descricao' => $config['descricao'] ?? null,
                'preco_adicional' => $config['preco_adicional'] ?? 0,
                'ordem' => $config['ordem'] ?? 0,
            ]);
        }

        return redirect()->route('produtos.configuracoes.index')->with('success', 'Configuracao atualizada com sucesso!');
    }

    public function destroy($produtoId, $opcaoId)
    {
        $opcao = ProdutoOpcao::findOrFail($opcaoId);
        $opcao->delete();

        return redirect()->route('produtos.configuracoes.index')->with('success', 'Configuracao removida com sucesso!');
    }
}
