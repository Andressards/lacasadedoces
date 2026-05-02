@extends('layouts.main')

@section('content')
<div class="create-container">
    <div class="header-container">
        <h1 class="titulo-form" id="titulo-form-membro">Gerenciar Pedido</h1>
    </div>

    <form action="{{ route('pedidos.atualizar', ['id' => $pedido->id]) }}" method="POST" enctype="multipart/form-data">
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

        <div class="mb-3">
            <label class="form-label">Imagem Atual</label>
            <div class="image-preview mb-2">
                @if($pedido->foto_inspiracao)
                    @php
                        // Verifica se o que está no banco já é uma URL completa
                        $isUrl = str_contains($pedido->foto_inspiracao, 'http');
                        $urlImagem = $isUrl ? $pedido->foto_inspiracao : asset('storage/' . $pedido->foto_inspiracao);
                    @endphp

                    <img src="{{ $urlImagem }}" 
                        alt="Preview" 
                        style="max-width: 200px; border-radius: 8px; border: 1px solid #ddd;">
                @else
                    <p class="text-muted">Nenhuma imagem cadastrada.</p>
                @endif
            </div>

            <label for="image" class="form-label">Alterar Imagem</label>
            <input type="file" name="image" id="image" class="form-control">
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
                        @php
                            $selectedConfigs = $item->configuracoes->groupBy('produto_opcao_id')
                                ->map(function($group) {
                                    return $group->pluck('produto_configuracao_id')->toArray();
                                })->toArray();
                        @endphp
                        <tr class="item-row" data-selected-configs='@json($selectedConfigs)'>
                            <td>
                                <input type="hidden" name="itens[{{ $index }}][id]" value="{{ $item->id }}">
                                <select name="itens[{{ $index }}][produto_id]" class="form-select produto-select" data-index="{{ $index }}">
                                    @foreach($produtos as $produto)
                                        <option value="{{ $produto->id }}" {{ $item->produto_id == $produto->id ? 'selected' : '' }}>
                                            {{ $produto->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="configuracoes-container mt-2"></div>
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
    @php
        $produtosOpcoes = $produtos->mapWithKeys(function($produto) {
            return [$produto->id => [
                'opcoes' => $produto->opcoes->map(function($opcao) {
                    return [
                        'id' => $opcao->id,
                        'nome' => $opcao->nome,
                        'tipo' => $opcao->tipo,
                        'obrigatorio' => $opcao->obrigatorio,
                        'quantidade_minima' => $opcao->quantidade_minima,
                        'quantidade_maxima' => $opcao->quantidade_maxima,
                        'configuracoes' => $opcao->configuracoes->map(function($config) {
                            return [
                                'id' => $config->id,
                                'valor' => $config->valor,
                                'preco_adicional' => $config->preco_adicional,
                                'descricao' => $config->descricao,
                            ];
                        })->toArray(),
                    ];
                })->toArray(),
            ]];
        })->toArray();
    @endphp

    // Dados dos produtos para preço e opções
    const produtosData = @json($produtos->pluck('preco', 'id'));
    const produtosOpcoes = @json($produtosOpcoes);

    function renderConfiguracoesRow(row, produtoId, selectedConfigs = {}) {
        const container = row.querySelector('.configuracoes-container');
        container.innerHTML = '';

        if (!produtoId || !produtosOpcoes[produtoId] || produtosOpcoes[produtoId].opcoes.length === 0) {
            container.innerHTML = '<em>Nenhuma configuração</em>';
            return;
        }

        let html = '';
        const index = row.querySelector('.produto-select').dataset.index;
        produtosOpcoes[produtoId].opcoes.forEach(opcao => {
            const configs = opcao.configuracoes || [];
            if (configs.length === 0) {
                return;
            }

            const selected = selectedConfigs[opcao.id] || [];
            const selectedArray = Array.isArray(selected) ? selected.map(String) : [String(selected)];

            html += '<div class="opcao-group mb-2 p-2 border rounded">';
            html += '<div class="fw-bold mb-1">' + opcao.nome + (opcao.obrigatorio ? ' <span class="text-danger">*</span>' : '') + '</div>';

            if (opcao.tipo === 'selecao_unica') {
                configs.forEach(config => {
                    const checked = selectedArray.includes(String(config.id)) ? 'checked' : '';
                    html += '<div class="form-check">';
                    html += '<input class="form-check-input configuracao-input" type="radio" name="itens[' + index + '][configuracoes][' + opcao.id + '][]" value="' + config.id + '" data-preco-adicional="' + config.preco_adicional + '" ' + checked + ' ' + (opcao.obrigatorio ? 'required' : '') + '>';
                    html += '<label class="form-check-label">' + config.valor;
                    if (config.preco_adicional > 0) {
                        html += ' <small class="text-success">(+R$ ' + Number(config.preco_adicional).toFixed(2).replace('.', ',') + ')</small>';
                    }
                    if (config.descricao) {
                        html += '<br><small class="text-muted">' + config.descricao + '</small>';
                    }
                    html += '</label></div>';
                });
            } else if (opcao.tipo === 'selecao_multipla') {
                configs.forEach(config => {
                    const checked = selectedArray.includes(String(config.id)) ? 'checked' : '';
                    html += '<div class="form-check">';
                    html += '<input class="form-check-input configuracao-input" type="checkbox" name="itens[' + index + '][configuracoes][' + opcao.id + '][]" value="' + config.id + '" data-preco-adicional="' + config.preco_adicional + '" ' + checked + '>';
                    html += '<label class="form-check-label">' + config.valor;
                    if (config.preco_adicional > 0) {
                        html += ' <small class="text-success">(+R$ ' + Number(config.preco_adicional).toFixed(2).replace('.', ',') + ')</small>';
                    }
                    if (config.descricao) {
                        html += '<br><small class="text-muted">' + config.descricao + '</small>';
                    }
                    html += '</label></div>';
                });
            } else {
                html += '<div class="small text-muted">Configuração fixa: ' + configs.map(config => config.valor).join(', ') + '</div>';
                configs.forEach(config => {
                    html += '<input type="hidden" name="itens[' + index + '][configuracoes][' + opcao.id + '][]" value="' + config.id + '">';
                });
            }

            html += '</div>';
        });

        container.innerHTML = html;
    }

    function atualizarPrecoDoItem(row) {
        const produtoId = row.querySelector('.produto-select').value;
        const precoInput = row.querySelector('.preco-input');
        let precoBase = parseFloat(produtosData[produtoId] || 0);
        let adicional = 0;
        row.querySelectorAll('.configuracao-input:checked').forEach(input => {
            adicional += parseFloat(input.dataset.precoAdicional || 0);
        });
        precoInput.value = (precoBase + adicional).toFixed(2);
    }

    function atualizarSubtotalDoItem(row) {
        const preco = parseFloat(row.querySelector('.preco-input').value) || 0;
        const quantidade = parseFloat(row.querySelector('.quantidade-input').value) || 0;
        const subtotal = preco * quantidade;
        row.querySelector('.subtotal').textContent = 'R$ ' + subtotal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        return subtotal;
    }

    function calcularTotais() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            total += atualizarSubtotalDoItem(row);
        });
        document.getElementById('total-pedido').textContent = 'R$ ' + total.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

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

    // Atualizar preço quando produto ou configuração é modificada
    document.addEventListener('change', (e) => {
        if (e.target.classList.contains('produto-select')) {
            const produtoId = e.target.value;
            const row = e.target.closest('.item-row');
            const precoInput = row.querySelector('.preco-input');
            precoInput.value = produtosData[produtoId] || 0;
            renderConfiguracoesRow(row, produtoId);
            atualizarPrecoDoItem(row);
            calcularTotais();
        }

        if (e.target.classList.contains('configuracao-input') || e.target.classList.contains('quantidade-input')) {
            const row = e.target.closest('.item-row');
            atualizarPrecoDoItem(row);
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
        newRow.dataset.selectedConfigs = JSON.stringify({});
        newRow.innerHTML = `
            <td>
                <input type="hidden" name="itens[${index}][id]" value="">
                <select name="itens[${index}][produto_id]" class="form-select produto-select" data-index="${index}">
                    <option value="">Selecione um produto</option>
                    ${produtosOptions}
                </select>
                <div class="configuracoes-container mt-2"><em>Nenhuma configuração</em></div>
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
        document.querySelectorAll('.item-row').forEach(row => {
            const produtoId = row.querySelector('.produto-select').value;
            const selectedConfigs = row.dataset.selectedConfigs ? JSON.parse(row.dataset.selectedConfigs) : {};
            renderConfiguracoesRow(row, produtoId, selectedConfigs);
            atualizarPrecoDoItem(row);
        });
        calcularTotais();
        // Disparar mudança de tipo_entrega para carregar estado correto
        document.getElementById('tipo_entrega').dispatchEvent(new Event('change'));
    });
</script>
@endsection
