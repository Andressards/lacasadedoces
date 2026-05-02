@extends('layouts.main')

@section('content')
<div class="create-container">
    <div class="header-container">
        <h1 class="titulo-form" id="titulo-form-membro">Criar Pedido Manualmente</h1>
    </div>

    <form action="{{ route('pedidos.salvar') }}" method="POST" enctype="multipart/form-data">
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
            <label for="numero_contato" class="form-label">Número para contato</label>
            <input type="text" name="numero_contato" id="numero_contato" class="form-control">
        </div>

        <div class="mb-3">
            <label for="observacao" class="form-label">Observação</label>
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

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="numero" class="form-label">Nº</label>
                    <input type="text" name="numero" id="numero" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="bairro" class="form-label">Bairro</label>
                    <input type="text" name="bairro" id="bairro" class="form-control">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="quadra" class="form-label">Quadra</label>
                    <input type="number" name="quadra" id="quadra" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="lote" class="form-label">Lote</label>
                    <input type="number" name="lote" id="lote" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="cep" class="form-label">CEP</label>
                    <input type="number" name="cep" id="cep" class="form-control">
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="foto_inspiracao" class="form-label">Imagem de Inspiração</label>
            <input type="file" name="foto_inspiracao" id="foto_inspiracao" class="form-control">
        </div>

        <h4>Itens do Pedido</h4>
        <div id="itens-container">
            <div class="mb-3 item-pedido">
                <label class="form-label">Produto</label>
                <select name="itens[0][produto_id]" class="form-control select-produto" required>
                    <option value="">Selecione um produto</option>
                    @foreach($produtos as $produto)
                        <option value="{{ $produto->id }}">{{ $produto->nome }} (R$ {{ number_format($produto->preco, 2, ',', '.') }})</option>
                    @endforeach
                </select>

                <label class="form-label">Quantidade</label>
                <input type="number" name="itens[0][quantidade]" class="form-control" required min="1">

                <button type="button" class="btn btn-danger btn-sm remove-item" style="margin-top: 10px;">Remover</button>
            </div>
        </div>

        <button type="button" id="add-item" class="btn btn-success">Adicionar Item</button>

        <div class="btn-container">
            <a href="{{ route('pedidos.gerenciar') }}" class="btn btn-primary">Voltar</a>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Confirmar Pedido</button>
    </form>
</div>

<script>
    const produtos = @json($produtos);

    document.getElementById('add-item').addEventListener('click', function() {
        let container = document.getElementById('itens-container');
        let index = container.getElementsByClassName('item-pedido').length;

        let div = document.createElement('div');
        div.classList.add('mb-3', 'item-pedido');

        let selectOptions = '<option value="">Selecione um produto</option>';
        produtos.forEach(produto => {
            selectOptions += `<option value="${produto.id}">${produto.nome} (R$ ${parseFloat(produto.preco).toFixed(2).replace('.', ',')})</option>`;
        });

        div.innerHTML = `
            <label class="form-label">Produto</label>
            <select name="itens[${index}][produto_id]" class="form-control select-produto" required>
                ${selectOptions}
            </select>

            <label class="form-label">Quantidade</label>
            <input type="number" name="itens[${index}][quantidade]" class="form-control" required min="1">

            <button type="button" class="btn btn-danger btn-sm remove-item" style="margin-top: 10px;">Remover</button>
        `;

        container.appendChild(div);
    });

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-item')) {
            event.target.parentElement.remove();
        }
    });

    document.getElementById('tipo_entrega').addEventListener('change', function() {
        const container = document.getElementById('endereco-container');
        if (this.value === 'entrega') {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    });
</script>
@endsection
