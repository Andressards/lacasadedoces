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
        return view('produtos.configuracoes.index', compact('produtos'));
    }

    public function create($produtoId)
    {
        $produto = Produtos::findOrFail($produtoId);
        return view('produtos.configuracoes.create', compact('produto'));
    }

    public function store(Request $request, $produtoId)
    {
        $request->validate([
            'categoria' => 'required|string',
            'nome' => 'required|string',
            'tipo' => 'required|in:selecao_unica,selecao_multipla,quantidade_fixa',
            'obrigatorio' => 'boolean',
            'configuracoes' => 'required|array|min:1',
            'configuracoes.*.valor' => 'required|string',
        ]);

        $produto = Produtos::findOrFail($produtoId);

        // Criar a opção
        $opcao = ProdutoOpcao::create([
            'produto_id' => $produto->id,
            'categoria' => $request->categoria,
            'nome' => $request->nome,
            'tipo' => $request->tipo,
            'quantidade_minima' => $request->quantidade_minima,
            'quantidade_maxima' => $request->quantidade_maxima,
            'obrigatorio' => $request->obrigatorio ?? true,
            'ordem' => ProdutoOpcao::where('produto_id', $produto->id)->max('ordem') + 1,
        ]);

        // Criar as configurações
        foreach ($request->configuracoes as $config) {
            ProdutoConfiguracao::create([
                'produto_opcao_id' => $opcao->id,
                'valor' => $config['valor'],
                'descricao' => $config['descricao'] ?? null,
                'preco_adicional' => $config['preco_adicional'] ?? 0,
                'ordem' => $config['ordem'] ?? 0,
            ]);
        }

        return redirect()->route('produtos.configuracoes.index')->with('success', 'Configuração criada com sucesso!');
    }

    public function edit($produtoId, $opcaoId)
    {
        $produto = Produtos::findOrFail($produtoId);
        $opcao = ProdutoOpcao::with('configuracoes')->findOrFail($opcaoId);
        return view('produtos.configuracoes.edit', compact('produto', 'opcao'));
    }

    public function update(Request $request, $produtoId, $opcaoId)
    {
        $request->validate([
            'categoria' => 'required|string',
            'nome' => 'required|string',
            'tipo' => 'required|in:selecao_unica,selecao_multipla,quantidade_fixa',
            'obrigatorio' => 'boolean',
            'configuracoes' => 'required|array|min:1',
            'configuracoes.*.valor' => 'required|string',
        ]);

        $opcao = ProdutoOpcao::findOrFail($opcaoId);

        // Atualizar a opção
        $opcao->update([
            'categoria' => $request->categoria,
            'nome' => $request->nome,
            'tipo' => $request->tipo,
            'quantidade_minima' => $request->quantidade_minima,
            'quantidade_maxima' => $request->quantidade_maxima,
            'obrigatorio' => $request->obrigatorio ?? true,
        ]);

        // Deletar configurações existentes
        $opcao->configuracoes()->delete();

        // Criar novas configurações
        foreach ($request->configuracoes as $config) {
            ProdutoConfiguracao::create([
                'produto_opcao_id' => $opcao->id,
                'valor' => $config['valor'],
                'descricao' => $config['descricao'] ?? null,
                'preco_adicional' => $config['preco_adicional'] ?? 0,
                'ordem' => $config['ordem'] ?? 0,
            ]);
        }

        return redirect()->route('produtos.configuracoes.index')->with('success', 'Configuração atualizada com sucesso!');
    }

    public function destroy($produtoId, $opcaoId)
    {
        $opcao = ProdutoOpcao::findOrFail($opcaoId);
        $opcao->delete();

        return redirect()->route('produtos.configuracoes.index')->with('success', 'Configuração removida com sucesso!');
    }
}
