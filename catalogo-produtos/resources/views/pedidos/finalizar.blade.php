@extends('layouts.main')

@section('content')
<div class="create-container">
    <div class="header-container">
        <h1 class="titulo-form" id="titulo-form-membro">Finalizar Pedido</h1>
    </div>

    <form action="{{ route('pedido.salvar') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nome_cliente" class="form-label">Nome do Cliente</label>
            <input type="text" name="nome_cliente" id="nome_cliente" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="data_entrega" class="form-label">Data de Entrega</label>
            <input type="datetime-local" name="data_entrega" id="data_entrega" class="form-control" required>
        </div>  

        <div class="mb-3">
            <label for="numero-contato" class="form-label">Número para contato</label>
            <input type="text" name="numero_contato" id="numero_contato" class="form-control">
        </div>

        <div class="mb-3">
            <label for="obs" class="form-label">Observação</label>
            <input type="text" name="observacao" id="observacao" class="form-control">
        </div>

        <div class="mb-3">
            <label for="tipo_entrega" class="form-label">Tipo de Entrega</label>
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
                <label for="numero" class="form-label">Nº</label>
                <input type="text" name="numero" id="numero" class="form-control">
            </div>

            <div class="mb-3">
                <label for="quadra" class="form-label">Quadra</label>
                <input type="number" name="quadra" id="quadra" class="form-control">
            </div>

            <div class="mb-3">
                <label for="lote" class="form-label">Lote</label>
                <input type="number" name="lote" id="lote" class="form-control">
            </div>

            <div class="mb-3">
                <label for="bairro" class="form-label">Bairro</label>
                <input type="text" name="bairro" id="bairro" class="form-control">
            </div>

            <div class="mb-3">
                <label for="cep" class="form-label">CEP</label>
                <input type="number" name="cep" id="cep" class="form-control">
            </div>
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
