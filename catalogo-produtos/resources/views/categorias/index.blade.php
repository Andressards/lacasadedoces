@extends('layouts.main')

@section('content')
    <div class="list-container">
        <h1 class="grid-tipo-entrada-title-container">Lista de Categorias</h1>
        <div class="btn-container">
            <a href="{{ route('categorias.create') }}" class="btn btn-primary">Novo</a>
        </div>

        <div class="filtro-container">
            <form action="{{ route('categorias.index') }}" method="GET" class="form-busca">
                <input type="text" name="search" placeholder="Buscar categoria..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-secondary">Buscar</button>
            </form>
        </div>

        @if(session('msg'))
            <p class="alert alert-success">{{ session('msg') }}</p>
        @endif

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Categoria</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categorias as $categoria) 
                    <tr>
                        <td>{{ $categoria->id }}</td>
                        <td>{{ $categoria->nome }}</td>

                        <td>
                            <a href="{{ route('categorias.toggleStatus', $categoria->id) }}" 
                               class="btn btn-{{ $categoria->ativo ? 'success' : 'danger' }}">
                                {{ $categoria->ativo ? 'Ativo' : 'Inativo' }}
                            </a>
                        </td>

                        <td>
                            <a href="{{ route('categorias.edit', $categoria->id) }}" class="btn btn-warning">Editar</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
