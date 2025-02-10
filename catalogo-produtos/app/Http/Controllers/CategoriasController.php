<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriasController extends Controller
{
    public function create()
    {
        return view('categorias.create'); // Exibe o formulário de cadastro
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:categorias',
        ]);

        Categoria::create([
            'nome' => $request->nome,
        ]);

        return redirect()->route('categorias.create')->with('msg', 'Categoria cadastrada com sucesso!');
    }

    public function edit($id)
    {
        $categoria = Categoria::findOrFail($id);
        return view('categorias.edit', compact('categoria'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:categorias,nome,' . $id,
        ]);

        $categoria = Categoria::findOrFail($id);
        $categoria->update([
            'nome' => $request->nome,
        ]);

        return redirect()->route('categorias.create')->with('msg', 'Categoria atualizada com sucesso!');
    }

    public function index()
    {
        $categorias = Categoria::all();
        return view('categorias.index', compact('categorias'));
    }

    public function toggleStatus($id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->ativo = !$categoria->ativo;  // Alterna o valor de 'ativo'
        $categoria->save();

        return redirect()->route('categorias.index');
    }

}
