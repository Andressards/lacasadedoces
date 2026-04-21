@extends('layouts.main')

@section('content')
<div class="create-container">
    <div class="header-container">
        <h1 class="titulo-form" id="titulo-form-membro">Finalizar Pedido</h1>
    </div>

    <form action="{{ route('pedido.salvar') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nome_cliente" class="form-label required-label">Nome do Cliente</label>
            <input type="text" name="nome_cliente" id="nome_cliente" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="data_entrega" class="form-label required-label">Data de Entrega</label>
            <input type="datetime-local" 
                name="data_entrega" 
                class="form-control @error('data_entrega') is-invalid @enderror" 
                value="{{ old('data_entrega') }}" 
                required 
                min="{{ date('Y-m-d\TH:i', strtotime('+3 hours')) }}">

            @error('data_entrega')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            <small class="text-muted">Pedidos com mínimo de 3 horas de antecedência, até às 20h.</small>

        </div>  

        <div class="mb-3">
            <label for="telefone" class="form-label required-label">Telefone de Contato</label>
            <input type="text" 
                name="telefone" 
                id="telefone" 
                class="form-control" 
                placeholder="(00) 00000-0000"
                oninput="mascaraTelefone(this)" 
                maxlength="15">
        </div>


        <div class="mb-3">
            <label for="obs" class="form-label">Observação</label>
            <input type="text" name="observacao" id="observacao" class="form-control">
        </div>

        <div class="mb-3">
            <label for="tipo_entrega" class="form-label required-label">Tipo de Entrega</label>
            <select name="tipo_entrega" id="tipo_entrega" class="form-control" required>
                <option value="retirada">Retirada</option>
                <option value="entrega">Entrega</option>
            </select>
        </div>


        <div id="endereco-container" style="display: none;">
            <div class="mb-3">
                <label for="rua" class="form-label">Rua</label>
                <input type="text" name="rua" id="rua" class="form-control">
            </div>

            <div class="mb-3">
                <label for="numero" class="form-label required-label">Nº</label>
                <input type="text" name="numero" id="numero" class="form-control">
            </div>

            <div class="mb-3">
                <label for="quadra" class="form-label required-label">Quadra</label>
                <input type="number" name="quadra" id="quadra" class="form-control">
            </div>

            <div class="mb-3">
                <label for="lote" class="form-label required-label">Lote</label>
                <input type="number" name="lote" id="lote" class="form-control">
            </div>

            <div class="mb-3">
                <label for="bairro" class="form-label required-label">Bairro</label>
                <input type="text" name="bairro" id="bairro" class="form-control">
            </div>

            <div class="mb-3">
                <label for="cep" class="form-label required-label">CEP</label>
                <input type="number" name="cep" id="cep" class="form-control">
            </div>
        </div> 

        <h4>Itens do Pedido</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Configurações</th>
                    <th>Quantidade</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($itensCarrinho as $id => $item)
                    <tr>
                        <td>{{ $item['nome'] }}</td>
                        <td>
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
                                Nenhuma configuração
                            @endif
                        </td>
                        <td>{{ $item['quantidade'] }}</td>
                        <td>
                            <a href="{{ route('carrinho.editarConfiguracao', $id) }}" class="btn btn-warning btn-sm">Editar Configuração</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Confirmar Pedido</button>
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
        } else {
            enderecoContainer.style.display = 'none';
            document.getElementById('rua').removeAttribute('required');
            document.getElementById('bairro').removeAttribute('required');
            document.getElementById('numero').removeAttribute('required');
        }
    });
</script>
@endsection
