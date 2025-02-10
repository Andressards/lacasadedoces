@extends('layouts.main')

@section('content')
    <div class="create-container">
        <div class="header-container">
            <h1 class="titulo-form" id="titulo-form-membro">Editar Categorias</h1>
        </div>

        @if(session('msg'))
            <p class="alert alert-success">{{ session('msg') }}</p>
        @endif

        <div class="form">
            <form action="{{ route('categorias.update', $categoria->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="nome">Nome da Categoria:</label>
                    <input type="text" class="form-control" id="nome" name="nome" 
                           value="{{ old('nome', $categoria->nome) }}" required>
                </div>
                <div class="btn-container">
                    <input type="submit" class="btn btn-primary" value="Atualizar">
                </div>
                <div class="btn-container">
                    <a href="{{ route('categorias.index') }}" class="btn btn-primary">Voltar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
