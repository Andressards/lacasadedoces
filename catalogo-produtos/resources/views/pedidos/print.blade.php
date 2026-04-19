@extends('layouts.main')

@section('content')
<div class="print-container">
    <div class="print-header">
        <h1 class="titulo-form">Imprimir Pedido #{{ $pedido->id }}</h1>
    </div>

    <div class="print-content">
        <h2>Dados do Pedido</h2>
        <div class="pedido-info">
            <p><strong>Cliente:</strong> {{ $pedido->nome_cliente }}</p>
            <p><strong>Status:</strong> {{ $pedido->status }}</p>
            <p><strong>Data de registro:</strong> {{ \Carbon\Carbon::parse($pedido->created_at)->format('d/m/Y H:i') }}</p>
            <p><strong>Data de entrega:</strong> {{ \Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y H:i') }}</p>
            <p><strong>Contato:</strong> {{ $pedido->numero_contato ?? '-' }}</p>

            @if($pedido->tipo_entrega === 'entrega')
                <p><strong>Endereço de entrega:</strong> {{ $pedido->rua }}, Nº {{ $pedido->numero }}, Bairro {{ $pedido->bairro }}{{ $pedido->quadra ? ", Quadra {$pedido->quadra}" : '' }}{{ $pedido->lote ? ", Lote {$pedido->lote}" : '' }}{{ $pedido->cep ? ", CEP {$pedido->cep}" : '' }}</p>
            @else
                <p><strong>Tipo de entrega:</strong> Retirada</p>
            @endif

            @if($pedido->observacao)
                <p><strong>Observação:</strong> {{ $pedido->observacao }}</p>
            @endif
        </div>

        <h3 class="mt-4">Itens do Pedido</h3>
        <table class="table table-bordered print-table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Preço unitário</th>
                    <th>Subtotal</th>
                    <th>Configurações</th>
                </tr>
            </thead>
            <tbody>
                @php $totalPedido = 0; @endphp
                @foreach($pedido->itens as $item)
                    @php
                        $itemTotal = $item->preco_unitario * $item->quantidade;
                        $totalPedido += $itemTotal;
                    @endphp
                    <tr>
                        <td>{{ $item->produto->nome ?? 'Produto removido' }}</td>
                        <td>{{ $item->quantidade }}</td>
                        <td>R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($itemTotal, 2, ',', '.') }}</td>
                        <td>
                            @if($item->configuracoes->isNotEmpty())
                                <ul class="mb-0">
                                    @foreach($item->configuracoes as $config)
                                        <li>
                                            {{ $config->produtoConfiguracao->valor ?? 'Configuração removida' }}
                                            @if(isset($config->produtoConfiguracao->preco_adicional) && $config->produtoConfiguracao->preco_adicional > 0)
                                                (+R$ {{ number_format($config->produtoConfiguracao->preco_adicional, 2, ',', '.') }})
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span>Sem configurações</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Total do pedido</th>
                    <th colspan="2">R$ {{ number_format($totalPedido, 2, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="print-actions">
        <button id="print-button" class="btn btn-primary">Imprimir pedido</button>
        <a href="{{ route('pedidos.gerenciar') }}" class="btn btn-secondary ms-2">Voltar</a>
    </div>
</div>

<style>
@media print {
    .print-actions {
        display: none !important;
    }
    .print-container {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 20px !important;
    }
    .print-content {
        margin-bottom: 20px !important;
    }
}

.print-container {
    max-width: 100%;
    margin: 0 auto;
    padding: 20px;
}

.print-header {
    text-align: center;
    margin-bottom: 30px;
}

.print-content {
    margin-bottom: 30px;
}

.pedido-info p {
    margin-bottom: 8px;
    font-size: 14px;
}

.print-table {
    width: 100%;
    font-size: 12px;
}

.print-table th,
.print-table td {
    padding: 8px;
    text-align: left;
}

.print-actions {
    text-align: center;
    margin-top: 20px;
}
</style>

<script>
    document.getElementById('print-button').addEventListener('click', function() {
        window.print();
    });
</script>
@endsection
