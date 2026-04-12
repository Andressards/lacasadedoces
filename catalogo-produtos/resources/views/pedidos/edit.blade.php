@extends('layouts.main')

@section('content')
<div class="create-container">
    <div class="header-container">
        <h1 class="titulo-form" id="titulo-form-membro">Gerenciar Pedido</h1>
    </div>

    <form action="{{ route('pedidos.atualizar', ['id' => $pedido->id]) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nome_cliente" class="form-label">Nome do Cliente</label>
            <input type="text" name="nome_cliente" id="nome_cliente" class="form-control" value="{{ $pedido->nome_cliente }}" required>
        </div>

        <div class="mb-3">
            <label for="data_entrega" class="form-label">Data de Entrega</label>
            <input type="datetime-local" name="data_entrega" id="data_entrega" class="form-control" value="{{ $pedido->data_entrega }}" required>
        </div>  

        <div class="mb-3">
            <label for="numero-contato" class="form-label">Número para contato</label>
            <input type="text" name="numero_contato" id="numero_contato" class="form-control" value="{{ $pedido->numero_contato }}">
        </div>

        <div class="mb-3">
            <label for="obs" class="form-label">Observação</label>
            <input type="text" name="observacao" id="observacao" class="form-control" value="{{ $pedido->observacao }}">
        </div>

        <div class="mb-3">
            <label for="tipo_entrega" class="form-label">Tipo de Entrega</label>
            <select name="tipo_entrega" id="tipo_entrega" class="form-control" required>
                <option value="retirada" {{ $pedido->tipo_entrega === 'retirada' ? 'selected' : '' }}>Retirada</option>
                <option value="entrega" {{ $pedido->tipo_entrega === 'entrega' ? 'selected' : '' }}>Entrega</option>
            </select>
        </div>


        <div id="endereco-container" style="display: {{ $pedido->tipo_entrega === 'entrega' ? 'block' : 'none' }};">
            <div class="mb-3">
                <label for="rua" class="form-label">Rua</label>
                <input type="text" name="rua" id="rua" class="form-control" value="{{ $pedido->rua }}">
            </div>

            <div class="mb-3">
                <label for="numero" class="form-label">Nº</label>
                <input type="text" name="numero" id="numero" class="form-control" value="{{ $pedido->numero }}">
            </div>

            <div class="mb-3">
                <label for="quadra" class="form-label">Quadra</label>
                <input type="number" name="quadra" id="quadra" class="form-control" value="{{ $pedido->quadra }}">
            </div>

            <div class="mb-3">
                <label for="lote" class="form-label">Lote</label>
                <input type="number" name="lote" id="lote" class="form-control" value="{{ $pedido->lote }}">
            </div>

            <div class="mb-3">
                <label for="bairro" class="form-label">Bairro</label>
                <input type="text" name="bairro" id="bairro" class="form-control" value="{{ $pedido->bairro }}">
            </div>

            <div class="mb-3">
                <label for="cep" class="form-label">CEP</label>
                <input type="number" name="cep" id="cep" class="form-control" value="{{ $pedido->cep }}">
            </div>
        </div> 

        <h4>Itens do Pedido</h4>
        <div class="table-responsive mb-3">
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Produto</th>
                        <th>Preço Unitário</th>
                        <th>Quantidade</th>
                        <th>Subtotal</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="itens-container">
                    @forelse($pedido->itens as $index => $item)
                        <tr class="item-row">
                            <td>
                                <input type="hidden" name="itens[{{ $index }}][id]" value="{{ $item->id }}">
                                <select name="itens[{{ $index }}][produto_id]" class="form-select produto-select" data-index="{{ $index }}">
                                    @foreach($produtos as $produto)
                                        <option value="{{ $produto->id }}" {{ $item->produto_id == $produto->id ? 'selected' : '' }}>
                                            {{ $produto->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="itens[{{ $index }}][preco]" class="form-control preco-input" value="{{ $item->produto->preco ?? 0 }}" step="0.01" readonly>
                            </td>
                            <td>
                                <input type="number" name="itens[{{ $index }}][quantidade]" class="form-control quantidade-input" value="{{ $item->quantidade }}" min="1" required>
                            </td>
                            <td>
                                <span class="subtotal">R$ 0,00</span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger remover-item">
                                    <i class="bi bi-trash"></i> Remover
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Nenhum item no pedido</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mb-3">
            <button type="button" class="btn btn-secondary" id="adicionar-item">
                <i class="bi bi-plus-lg"></i> Adicionar Item
            </button>
        </div>

        <div class="alert alert-info mb-3">
            <strong>Total do Pedido:</strong> <span id="total-pedido">R$ 0,00</span>
        </div>

        <div class="btn-container">
            <input type="submit" class="btn btn-primary" value="Atualizar">
        </div>
        <div class="btn-container">
            <a href="{{ route('pedidos.gerenciar') }}" class="btn btn-primary">Voltar</a>
        </div>
        
    </form>
</div>

<script>
    // Dados dos produtos para preço
    const produtosData = @json($produtos->pluck('preco', 'id'));

    // Toggle endereço container
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

    // Função para calcular subtotal e total
    function calcularTotais() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const preco = parseFloat(row.querySelector('.preco-input').value) || 0;
            const quantidade = parseFloat(row.querySelector('.quantidade-input').value) || 0;
            const subtotal = preco * quantidade;
            row.querySelector('.subtotal').textContent = 'R$ ' + subtotal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            total += subtotal;
        });
        document.getElementById('total-pedido').textContent = 'R$ ' + total.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Atualizar preço quando produto é selecionado
    document.addEventListener('change', (e) => {
        if (e.target.classList.contains('produto-select')) {
            const produtoId = e.target.value;
            const row = e.target.closest('.item-row');
            const precoInput = row.querySelector('.preco-input');
            precoInput.value = produtosData[produtoId] || 0;
            calcularTotais();
        }
    });

    // Atualizar total quando quantidade muda
    document.addEventListener('change', (e) => {
        if (e.target.classList.contains('quantidade-input')) {
            calcularTotais();
        }
    });

    // Remover item
    document.addEventListener('click', (e) => {
        if (e.target.closest('.remover-item')) {
            e.preventDefault();
            const row = e.target.closest('.item-row');
            row.remove();
            calcularTotais();
        }
    });

    // Adicionar novo item
    document.getElementById('adicionar-item').addEventListener('click', function() {
        const container = document.getElementById('itens-container');
        const index = container.children.length;
        
        let produtosOptions = '';
        @foreach($produtos as $produto)
            produtosOptions += '<option value="{{ $produto->id }}">{{ $produto->nome }}</option>';
        @endforeach

        const newRow = document.createElement('tr');
        newRow.classList.add('item-row');
        newRow.innerHTML = `
            <td>
                <input type="hidden" name="itens[${index}][id]" value="">
                <select name="itens[${index}][produto_id]" class="form-select produto-select" data-index="${index}">
                    <option value="">Selecione um produto</option>
                    ${produtosOptions}
                </select>
            </td>
            <td>
                <input type="number" name="itens[${index}][preco]" class="form-control preco-input" value="0" step="0.01" readonly>
            </td>
            <td>
                <input type="number" name="itens[${index}][quantidade]" class="form-control quantidade-input" value="1" min="1" required>
            </td>
            <td>
                <span class="subtotal">R$ 0,00</span>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remover-item">
                    <i class="bi bi-trash"></i> Remover
                </button>
            </td>
        `;
        container.appendChild(newRow);
        calcularTotais();
    });

    // Calcular totais ao carregar a página
    document.addEventListener('DOMContentLoaded', function() {
        calcularTotais();
        // Disparar mudança de tipo_entrega para carregar estado correto
        document.getElementById('tipo_entrega').dispatchEvent(new Event('change'));
    });
</script>
@endsection
