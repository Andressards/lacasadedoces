@extends('layouts.main')

@section('content')
    <div class="list-container">
        <h1 class="grid-tipo-entrada-title-container">Lista de Categorias</h1>

        @if(session('msg'))
            <p class="alert alert-success">{{ session('msg') }}</p>
        @endif

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Categoria</th>
                    <th>Status</th>  <!-- Coluna para o status de Ativo/Inativo -->
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categorias as $categoria)  <!-- Corrigir aqui: usar $categoria -->
                    <tr>
                        <td>{{ $categoria->id }}</td>
                        <td>{{ $categoria->nome }}</td>

                        <!-- Coluna Status de Ativo/Inativo -->
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
