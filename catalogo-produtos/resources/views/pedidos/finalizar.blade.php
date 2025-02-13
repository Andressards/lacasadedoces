@extends('layouts.main')

@section('content')
<div class="container">
    <h2>Finalizar Pedido</h2>

    <form action="{{ route('pedido.salvar') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nome_cliente" class="form-label">Nome do Cliente</label>
            <input type="text" name="nome_cliente" id="nome_cliente" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="data_entrega" class="form-label">Data de Entrega</label>
            <input type="date" name="data_entrega" id="data_entrega" class="form-control" required>
        </div>

        <h4>Itens do Pedido</h4>
        <ul class="list-group mb-3">
            @foreach($itensCarrinho as $item)
                <li class="list-group-item">
                    {{ $item['nome'] }} - Quantidade: {{ $item['quantidade'] }}
                </li>
            @endforeach
        </ul>

        <button type="submit" class="btn btn-primary">Confirmar Pedido</button>
    </form>
</div>
@endsection
