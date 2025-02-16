@extends('layouts.main')

@section('content')

    <div id="membro-create-container" class="create-container">
        <div class="header-container">
            <h1 class="titulo-form" id="titulo-form-membro">Registrar Produto</h1>
        </div>

        <div class="form">
            <form action="{{ route('produtos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" class="form-control" id="nome-produto" name="nome" placeholder="Digite o nome" required>
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição:</label>
                    <input type="text" class="form-control" id="descricao-produto" name="descricao" placeholder="Digite a descrição" required>
                </div>

                <div class="form-group">
                    <label for="preco">Preço:</label>
                    <input type="number" class="form-control" id="preco-produto" name="preco" placeholder="Digite o preço" required>
                </div>

                <!-- Seleção de Categoria -->
                <div class="form-group">
                    <label for="categoria_id">Categoria:</label>
                    <select name="categoria_id" id="categoria_id" class="form-control" required>
                        <option value="">Selecione uma categoria</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Input para imagem -->
                <div class="form-group">
                    <label for="imagem">Imagem do Produto:</label>
                    <input type="file" class="form-control" id="imagem" name="imagem">
                </div>

                <div class="btn-container">
                    <input type="submit" class="btn btn-primary" value="Cadastrar">
                </div>
                
                <div class="btn-container">
                    <a href="{{ route('produtos.index') }}" class="btn btn-primary">Voltar</a>
                </div>
            </form>
        </div>
    </div>

@endsection