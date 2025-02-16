@extends('layouts.main')

@section('content')
    <div class="list-container">
        <h1 class="grid-tipo-entrada-title-container">Lista de Produtos</h1>
        <div class="btn-container">
            <a href="{{ route('produtos.produtos_create') }}" class="btn btn-primary">Novo</a>
        </div>

        @if(session('msg'))
            <p class="alert alert-success">{{ session('msg') }}</p>
        @endif

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Produtos</th>
                    <th>Descrição</th>
                    <th>Categoria</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produtos as $produto)
                    <tr>
                        <td>{{ $produto->id }}</td>
                        <td>{{ $produto->nome }}</td>
                        <td>{{ $produto->descricao }}</td>
                        <td>{{ $produto->categoria->nome ?? 'Sem Categoria' }}</td>
                        <td>
                            <!-- Botão para alternar o status -->
                            <a href="{{ route('produtos.toggleStatus', $produto->id) }}" 
                               class="btn btn-{{ $produto->ativo ? 'success' : 'danger' }}">
                                {{ $produto->ativo ? 'Ativo' : 'Inativo' }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('produtos.edit', $produto->id) }}" class="btn btn-warning">Editar</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
