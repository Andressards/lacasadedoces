@extends('layouts.main')

@section('content')
    <div class="list-container">
        <h1 class="grid-tipo-entrada-title-container">Histórico de Pedidos</h1>
        <div class="btn-container">
            <a href="{{ route('pedidos.gerenciar') }}" class="btn btn-primary">Novo</a>
        </div>

        <div class="filtro-container">
            <form action="{{ route('pedidos.gerenciar') }}" method="GET" class="form-busca">
                <input type="text" name="search" placeholder="Buscar cliente..." value="{{ request('search') }}">
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
                    <th>Cliente</th>
                    <th>Data de registro</th>
                    <th>Data de entrega</th>
                    <th>Status</th>
                    <th>Ativar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedidos as $pedido)
                    <tr>
                        <td>{{ $pedido->id }}</td>
                        <td>{{ $pedido->nome_cliente }}</td>
                        <td>{{ \Carbon\Carbon::parse($pedido->created_at)->format('d/m/Y H:i' ) }}</td>
                        <td>{{ \Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y H:i' ) }}</td>
                        <td>{{ $pedido->status }}</td>
                        <td>
                            <a href="{{ route('pedidos.ativar', $pedido->id) }}" 
                            class="btn btn-danger" 
                            onclick="return confirm('Tem certeza que deseja mudar este pedido para pendente?');">
                                Ativar
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
