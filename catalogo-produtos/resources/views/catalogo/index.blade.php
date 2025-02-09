@extends('layouts.main')

@section('content')
    <!-- Título no topo -->
    <div class="titulo">
        <h1>Catálogo de Produtos</h1>
    </div>
    
    <!-- Cards de produtos abaixo do título -->
    <div class="catalogo-container">
        @foreach($produtos as $produto)
            <div class="produto-card">
                <img src="{{ asset('storage/' . $produto->imagem) }}" alt="{{ $produto->nome }}" class="produto-imagem">
                <h2>{{ $produto->nome }}</h2>
                <p>{{ $produto->descricao }}</p>
                <p>Preço: R$ {{ number_format($produto->preco, 2, ',', '.') }}</p>
                <a href="{{ route('catalogo.show', $produto->id) }}" class="btn btn-info">Ver Detalhes</a>
            </div>
        @endforeach
    </div>
@endsection
