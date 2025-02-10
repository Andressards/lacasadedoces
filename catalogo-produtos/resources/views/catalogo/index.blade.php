@extends('layouts.main')

@section('content')

<div class="catalogo-header">
    <h3 class="titulo"><strong>Catálogo de Produtos</strong></h3>

    <div class="filtro-container">
        <form action="{{ route('catalogo.index') }}" method="GET" class="form-busca">
            <input type="text" name="search" placeholder="Buscar produto..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-secondary">Buscar</button>
        </form>
    </div>
</div>

    <!-- Cards de produtos abaixo do título -->
    <div class="catalogo-container">
        @foreach($produtos as $produto)
            <div class="produto-card">
                <img src="{{ asset('storage/' . $produto->imagem) }}" alt="{{ $produto->nome }}" class="produto-imagem">
                <h4>{{ $produto->nome }}</h4>
                <p>Preço: R$ {{ number_format($produto->preco, 2, ',', '.') }}</p>
                <a href="{{ route('catalogo.show', $produto->id) }}" class="btn btn-info">Ver Detalhes</a>
            </div>
        @endforeach
    </div>
@endsection
