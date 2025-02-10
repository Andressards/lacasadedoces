<?php

namespace App\Http\Controllers;

use App\Models\Produtos;
use App\Models\Categoria;
use Illuminate\Http\Request;

class ProdutosController extends Controller
{
    public function create()
    {
        $categorias = Categoria::all(); // Busca todas as categorias cadastradas
        return view('produtos.produtos_create', compact('categorias'));
    }

    public function storeProdutos(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'preco' => 'required|numeric',
            'imagem' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        $produtos = new Produtos;

        $produtos->nome = $request->nome;
        $produtos->descricao = $request->descricao;
        $produtos->preco = $request->preco;
        $produtos->imagem = $request->caminhoImagem;
        $produtos->categoria_id = $request->categoria_id;

        // Verifica se há um arquivo de imagem e salva no storage
        if ($request->hasFile('imagem')) {
            $caminhoImagem = $request->file('imagem')->store('produtos', 'public');
            $produtos->imagem = $caminhoImagem;
        }

        $produtos->save();

        return redirect()->route('produtos.create')->with('msg', 'Produto cadastrado com sucesso!');
    }

    public function edit($id)
    {
        $produtos = Produtos::findOrFail($id);
        $categorias = Categoria::all(); // Obtém todas as categorias
        return view('produtos.edit', compact('produtos', 'categorias'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'preco' => 'required|numeric',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        $produtos = Produtos::findOrFail($id);

        // Atualizando os dados
        $produtos->nome = $request->nome;
        $produtos->descricao = $request->descricao;
        $produtos->preco = $request->preco;
        $produtos->categoria_id = $request->categoria_id;

        // Verifica se foi enviada uma nova imagem
        if ($request->hasFile('imagem')) {
            // Remove a imagem anterior, se houver
            if ($produtos->imagem) {
                Storage::delete('public/' . $produtos->imagem);
            }

            // Salva a nova imagem
            $caminhoImagem = $request->file('imagem')->store('produtos', 'public');
            $produtos->imagem = $caminhoImagem;
        }

        // Salva as alterações no produto
        $produtos->save();

        return redirect()->route('produtos.index')->with('msg', 'Produto atualizado com sucesso!');
    }


    public function index()
    {
        $produtos = Produtos::all();
        return view('produtos.index', compact('produtos'));
    }

    public function toggleStatus($id)
    {
        $produto = Produtos::findOrFail($id);

        // Alterna o status do produto
        $produto->ativo = !$produto->ativo;
        $produto->save();

        return redirect()->route('produtos.index');
    }

}


