@extends('layouts.main')

@section('content')
    <div class="produto-detalhes-container">
        <h1>{{ $produto->nome }}</h1>
        <img src="{{ asset('storage/' . $produto->imagem) }}" alt="{{ $produto->nome }}" class="produto-imagem">
        <p>{{ $produto->descricao }}</p>
        <p><strong>Preço:</strong> R$ {{ number_format($produto->preco, 2, ',', '.') }}</p>

        <!-- Formulário de Adicionar ao Carrinho -->
        <form action="{{ route('catalogo.adicionarCarrinho') }}" method="POST">
            @csrf
            <input type="hidden" name="produto_id" value="{{ $produto->id }}">
            <div class="form-group">
                <label for="quantidade">Quantidade:</label>
                <input type="number" name="quantidade" id="quantidade" min="1" required>
            </div>
            <button type="submit" class="btn btn-primary">Adicionar ao Carrinho</button>
        </form>
    </div>
@endsection
