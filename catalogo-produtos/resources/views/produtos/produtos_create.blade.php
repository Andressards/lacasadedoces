@extends('layouts.main')

@section('content')

    <div id="membro-create-container" class="create-container">
        <div class="header-container">
            <h1 class="titulo-form" id="titulo-form-membro">Registrar Produto</h1>
        </div>

        <div class="form">
        <form action="{{ route('produtos.store') }}" method="POST">
        @csrf
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" class="form-control" id="nome-produto" name="nome" placeholder="Digite o nome" required>
            </div>
            <div class="form-group">
                <label for="descricao">Descrição:</label>
                <input type="text" class="form-control" id="descricao-produto" name="descricao" placeholder="Digite o descrição do produto" required>
            </div>
            <div class="form-group">
                <label for="preco">Preço:</label>
                <input type="number" class="form-control" id="preco-produto" name="preco" placeholder="Digite o preço do produto" required>
            </div>
            <div class="btn-container">
                <input type="submit" class="btn btn-primary" value="Cadastrar">
            </div>
        </form>
    </div>

@endsection