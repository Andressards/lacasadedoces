@extends('layouts.checkout')

@section('content')
<div class="checkout-container">
    <div class="checkout-header">
        <h1>Finalizar Pedido</h1>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Erro ao validar o formulário:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pedido.salvar') }}" method="POST" class="checkout-form">
        @csrf

        <!-- Seção de Dados do Cliente -->
        <div class="form-section">
            <h3>Dados do Cliente</h3>

            <div class="form-group">
                <label for="nome_cliente" class="form-label required-label">Nome do Cliente</label>
                <input type="text" name="nome_cliente" id="nome_cliente" class="form-control @error('nome_cliente') is-invalid @enderror" value="{{ old('nome_cliente') }}" required>
                @error('nome_cliente')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="telefone" class="form-label required-label">Telefone de Contato</label>
                <input type="text" 
                    name="telefone" 
                    id="telefone" 
                    class="form-control @error('telefone') is-invalid @enderror" 
                    placeholder="(00) 00000-0000"
                    value="{{ old('telefone') }}"
                    oninput="mascaraTelefone(this)" 
                    maxlength="15"
                    required>
                @error('telefone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="observacao" class="form-label">Observação</label>
                <textarea name="observacao" id="observacao" class="form-control" rows="3">{{ old('observacao') }}</textarea>
            </div>
        </div>

        <!-- Seção de Entrega -->
        <div class="form-section">
            <h3>Informações de Entrega</h3>

            <div class="form-group">
                <label for="data_entrega" class="form-label required-label">Data de Entrega</label>
                <input type="datetime-local" 
                    name="data_entrega" 
                    id="data_entrega"
                    class="form-control @error('data_entrega') is-invalid @enderror" 
                    value="{{ old('data_entrega') }}" 
                    required 
                    min="{{ date('Y-m-d\TH:i', strtotime('+3 hours')) }}">
                @error('data_entrega')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text">Pedidos com mínimo de 3 horas de antecedência, até às 20h.</small>
            </div>

            <div class="form-group">
                <label for="tipo_entrega" class="form-label required-label">Tipo de Entrega</label>
                <select name="tipo_entrega" id="tipo_entrega" class="form-control @error('tipo_entrega') is-invalid @enderror" required>
                    <option value="retirada" {{ old('tipo_entrega') == 'retirada' ? 'selected' : '' }}>Retirada</option>
                    <option value="entrega" {{ old('tipo_entrega') == 'entrega' ? 'selected' : '' }}>Entrega</option>
                </select>
                @error('tipo_entrega')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Endereço (aparece apenas quando seleciona "Entrega") -->
            <div id="endereco-container" style="display: none;">
                <div class="form-group">
                    <label for="rua" class="form-label required-label">Rua</label>
                    <input type="text" name="rua" id="rua" class="form-control @error('rua') is-invalid @enderror" value="{{ old('rua') }}">
                    @error('rua')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="numero" class="form-label required-label">Nº</label>
                    <input type="text" name="numero" id="numero" class="form-control @error('numero') is-invalid @enderror" value="{{ old('numero') }}">
                    @error('numero')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="quadra" class="form-label required-label">Quadra</label>
                    <input type="number" name="quadra" id="quadra" class="form-control @error('quadra') is-invalid @enderror" value="{{ old('quadra') }}">
                    @error('quadra')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="lote" class="form-label required-label">Lote</label>
                    <input type="number" name="lote" id="lote" class="form-control @error('lote') is-invalid @enderror" value="{{ old('lote') }}">
                    @error('lote')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="bairro" class="form-label required-label">Bairro</label>
                    <input type="text" name="bairro" id="bairro" class="form-control @error('bairro') is-invalid @enderror" value="{{ old('bairro') }}">
                    @error('bairro')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="cep" class="form-label required-label">CEP</label>
                    <input type="number" name="cep" id="cep" class="form-control @error('cep') is-invalid @enderror" value="{{ old('cep') }}">
                    @error('cep')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Seção de Itens do Pedido -->
        <div class="pedido-itens-section">
            <h3>Itens do Pedido</h3>

            @if(empty($itensCarrinho))
                <p class="text-muted">Seu carrinho está vazio.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th class="table-hide-mobile">Configurações</th>
                                <th>Qtd</th>
                                <th class="table-hide-mobile">Valor</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($itensCarrinho as $id => $item)
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
                                    <td>{{ $item['quantidade'] }}</td>
                                    <td class="table-hide-mobile">R$ {{ number_format($item['preco'] + ($item['preco_adicional'] ?? 0), 2, ',', '.') }}</td>
                                    <td>
                                        <a href="{{ route('carrinho.editarConfiguracao', $id) }}" class="btn btn-sm btn-checkout">Editar</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Botões de ação -->
        <div class="btn-actions">
            <a href="/carrinho" class="btn btn-checkout-secondary">Voltar ao Carrinho</a>
            <button type="submit" class="btn btn-checkout">Confirmar Pedido</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('tipo_entrega').addEventListener('change', function() {
        let enderecoContainer = document.getElementById('endereco-container');
        if (this.value === 'entrega') {
            enderecoContainer.style.display = 'block';
            document.getElementById('rua').setAttribute('required', 'true');
            document.getElementById('bairro').setAttribute('required', 'true');
            document.getElementById('numero').setAttribute('required', 'true');
            document.getElementById('quadra').setAttribute('required', 'true');
            document.getElementById('lote').setAttribute('required', 'true');
            document.getElementById('cep').setAttribute('required', 'true');
        } else {
            enderecoContainer.style.display = 'none';
            document.getElementById('rua').removeAttribute('required');
            document.getElementById('bairro').removeAttribute('required');
            document.getElementById('numero').removeAttribute('required');
            document.getElementById('quadra').removeAttribute('required');
            document.getElementById('lote').removeAttribute('required');
            document.getElementById('cep').removeAttribute('required');
        }
    });
</script>
@endsection