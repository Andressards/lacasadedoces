@extends('layouts.main')

@section('content')
    <div class="create-container">
        <div class="header-container">
            <h1 class="titulo-form" id="titulo-form-produto">Editar Produto</h1>
        </div>

        @if(session('msg'))
            <p class="alert alert-success">{{ session('msg') }}</p>
        @endif

        <div class="form">
            <form action="{{ route('produtos.update', $produtos->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') <!-- Necessário para atualizar o produto -->
                
                <!-- Nome do Produto -->
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" class="form-control" id="nome" name="nome" 
                           value="{{ old('nome', $produtos->nome) }}" required>
                </div>

                <!-- Descrição do Produto -->
                <div class="form-group">
                    <label for="descricao">Descrição:</label>
                    <input type="text" class="form-control" id="descricao" name="descricao" 
                           value="{{ old('descricao', $produtos->descricao) }}" required>
                </div>

                <!-- Preço do Produto -->
                <div class="form-group">
                    <label for="preco">Preço:</label>
                    <input type="text" class="form-control" id="preco" name="preco" 
                           value="{{ old('preco', $produtos->preco) }}" required>
                </div>

                <!-- Categoria do Produto -->
                <div class="form-group">
                    <label for="categoria_id">Categoria:</label>
                    <select name="categoria_id" id="categoria_id" class="form-control" required>
                        <option value="">Selecione uma categoria</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" 
                                {{ old('categoria_id', $produtos->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Imagem do Produto -->
                <div class="form-group">
                    <label for="imagem">Imagem:</label>
                    <input type="file" class="form-control" id="imagem" name="imagem">
                    <!-- Exibe a imagem atual, caso exista -->
                    @if($produtos->imagem)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $produtos->imagem) }}" alt="Imagem do Produto" width="150">
                        </div>
                    @endif
                </div>

                <!-- Botões -->
                <div class="btn-container">
                    <input type="submit" class="btn btn-primary" value="Atualizar">
                </div>
                <div class="btn-container">
                    <a href="{{ route('produtos.index') }}" class="btn btn-primary">Voltar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
