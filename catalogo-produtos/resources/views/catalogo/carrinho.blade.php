@extends('layouts.checkout')

@section('content')
<div class="checkout-container">
    <div class="checkout-header">
        <h1>Meu Carrinho</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @php
        $itensCarrinho = session('carrinho', []);
        $totalCarrinho = 0;
    @endphp

    @if(empty($itensCarrinho))
        <div style="text-align: center; padding: 40px 20px;">
            <p style="font-size: 18px; color: #666; margin-bottom: 20px;">Seu carrinho está vazio.</p>
            <a href="/catalogo" class="btn btn-checkout">Continuar Comprando</a>
        </div>
    @else
        <div class="pedido-itens-section">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th class="table-hide-mobile">Configurações</th>
                            <th>Qtd</th>
                            <th class="table-hide-mobile">Preço Unit.</th>
                            <th class="table-hide-mobile">Total</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($itensCarrinho as $id => $item)
                            @php
                                $precoUnitario = $item['preco'] + ($item['preco_adicional'] ?? 0);
                                $totalItem = $precoUnitario * $item['quantidade'];
                                $totalCarrinho += $totalItem;
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $item['nome'] }}</strong>
                                    @if(isset($item['opcoes']) && !empty($item['opcoes']))
                                        <div style="font-size: 11px; color: #666; margin-top: 5px;">
                                            @foreach($item['opcoes'] as $opcaoId => $configIds)
                                                @php
                                                    $opcao = \App\Models\ProdutoOpcao::find($opcaoId);
                                                    $configIds = is_array($configIds) ? $configIds : [$configIds];
                                                @endphp
                                                @if($opcao)
                                                    <div><strong>{{ $opcao->nome }}:</strong></div>
                                                    @foreach($configIds as $configId)
                                                        @php
                                                            $configuracao = \App\Models\ProdutoConfiguracao::find($configId);
                                                        @endphp
                                                        @if($configuracao)
                                                            <div>- {{ $configuracao->valor }} (+R$ {{ number_format($configuracao->preco_adicional, 2, ',', '.') }})</div>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <div style="font-size: 11px; color: #999; margin-top: 5px;"><em>Sem configurações</em></div>
                                    @endif
                                </td>
                                <td class="table-hide-mobile">
                                    @if(isset($item['opcoes']) && !empty($item['opcoes']))
                                        @foreach($item['opcoes'] as $opcaoId => $configIds)
                                            @php
                                                $opcao = \App\Models\ProdutoOpcao::find($opcaoId);
                                                $configIds = is_array($configIds) ? $configIds : [$configIds];
                                            @endphp
                                            @if($opcao)
                                                <strong>{{ $opcao->nome }}:</strong><br>
                                                @foreach($configIds as $configId)
                                                    @php
                                                        $configuracao = \App\Models\ProdutoConfiguracao::find($configId);
                                                    @endphp
                                                    @if($configuracao)
                                                        - {{ $configuracao->valor }} (+R$ {{ number_format($configuracao->preco_adicional, 2, ',', '.') }})<br>
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @else
                                        <em>Nenhuma configuração</em>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('carrinho.atualizar', $id) }}" method="POST" style="display: flex; gap: 5px; flex-wrap: wrap;">
                                        @csrf
                                        <input type="number" name="quantidade" value="{{ $item['quantidade'] }}" min="1" style="width: 50px; padding: 5px; border: 1px solid #ddd; border-radius: 3px;">
                                        <button type="submit" class="btn btn-sm btn-checkout" style="padding: 5px 10px; font-size: 12px;">Atualizar</button>
                                    </form>
                                </td>
                                <td class="table-hide-mobile">R$ {{ number_format($precoUnitario, 2, ',', '.') }}</td>
                                <td class="table-hide-mobile">R$ {{ number_format($totalItem, 2, ',', '.') }}</td>
                                <td>
                                    <form action="{{ route('carrinho.remover', $id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" style="padding: 5px 10px; font-size: 12px;">Remover</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f8f9fa; font-weight: bold;">
                            <td colspan="4" style="text-align: right; padding: 15px;">Total do Carrinho:</td>
                            <td colspan="2" style="padding: 15px; color: #9a4f2f; font-size: 18px;">R$ {{ number_format($totalCarrinho, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="btn-actions">
            <a href="/catalogo" class="btn btn-checkout-secondary">Continuar Comprando</a>
            <a href="{{ route('pedido.formulario') }}" class="btn btn-checkout">Finalizar Pedido</a>
        </div>
    @endif
</div>
@endsection