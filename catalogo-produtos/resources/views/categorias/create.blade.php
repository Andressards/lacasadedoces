@extends('layouts.main')

@section('content')
    <div class="create-container">
        <div class="header-container">
            <h1 class="titulo-form" id="titulo-form-membro">Registrar Categorias</h1>
        </div>

        @if(session('msg'))
            <p class="alert alert-success">{{ session('msg') }}</p>
        @endif

        <div class="form">
            <form action="{{ route('categorias.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="nome">Nome da Categoria:</label>
                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Digite o nome da categoria" required>
                </div>
                <div class="btn-container">
                    <input type="submit" class="btn btn-primary" value="Cadastrar">
                </div>
            </form>
        </div>
    </div>
@endsection
