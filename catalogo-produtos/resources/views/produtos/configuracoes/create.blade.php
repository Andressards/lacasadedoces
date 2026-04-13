@extends('layouts.main')

@section('content')
<div class="create-container">
    <div class="header-container">
        <h1 class="titulo-form">Adicionar Configuração - {{ $produto->nome }}</h1>
    </div>

    <form action="{{ route('produtos.configuracoes.store', $produto->id) }}" method="POST" id="configuracao-form">
        @csrf

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="categoria" class="form-label">Categoria *</label>
                    <select name="categoria" id="categoria" class="form-control" required>
                        <option value="">Selecione uma categoria</option>
                        <option value="bolo">Bolo</option>
                        <option value="doces">Doces</option>
                        <option value="salgados">Salgados</option>
                        <option value="bebidas">Bebidas</option>
                        <option value="outros">Outros</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="tipo" class="form-label">Tipo de Seleção *</label>
                    <select name="tipo" id="tipo" class="form-control" required>
                        <option value="">Selecione o tipo</option>
                        <option value="selecao_unica">Seleção Única (cliente escolhe 1 opção)</option>
                        <option value="selecao_multipla">Seleção Múltipla (cliente escolhe várias opções)</option>
                        <option value="quantidade_fixa">Quantidade Fixa (itens pré-definidos)</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="nome" class="form-label">Nome da Opção *</label>
            <input type="text" name="nome" id="nome" class="form-control" required
                   placeholder="Ex: Sabor da Massa, Sabores dos Doces">
        </div>

        <div class="row" id="quantidade-container" style="display: none;">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="quantidade_minima" class="form-label">Quantidade Mínima</label>
                    <input type="number" name="quantidade_minima" id="quantidade_minima" class="form-control" min="1">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="quantidade_maxima" class="form-label">Quantidade Máxima</label>
                    <input type="number" name="quantidade_maxima" id="quantidade_maxima" class="form-control" min="1">
                </div>
            </div>
        </div>

        <div class="mb-3">
            <div class="form-check">
                <input type="checkbox" name="obrigatorio" id="obrigatorio" class="form-check-input" checked>
                <label for="obrigatorio" class="form-check-label">Opção obrigatória</label>
            </div>
        </div>

        <h4>Opções Disponíveis</h4>
        <div id="configuracoes-container">
            <div class="configuracao-item border p-3 mb-3 rounded">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Valor *</label>
                        <input type="text" name="configuracoes[0][valor]" class="form-control" required
                               placeholder="Ex: Chocolate, Brigadeiro">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Preço Adicional (R$)</label>
                        <input type="number" name="configuracoes[0][preco_adicional]" class="form-control" step="0.01" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Descrição</label>
                        <input type="text" name="configuracoes[0][descricao]" class="form-control"
                               placeholder="Descrição opcional">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-sm remover-configuracao">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <button type="button" class="btn btn-secondary" id="adicionar-configuracao">
                <i class="bi bi-plus-lg"></i> Adicionar Opção
            </button>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Salvar Configuração</button>
            <a href="{{ route('produtos.configuracoes.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<script>
document.getElementById('tipo').addEventListener('change', function() {
    const quantidadeContainer = document.getElementById('quantidade-container');
    if (this.value === 'selecao_multipla') {
        quantidadeContainer.style.display = 'block';
    } else {
        quantidadeContainer.style.display = 'none';
    }
});

let configuracaoIndex = 1;

document.getElementById('adicionar-configuracao').addEventListener('click', function() {
    const container = document.getElementById('configuracoes-container');
    const newItem = document.createElement('div');
    newItem.className = 'configuracao-item border p-3 mb-3 rounded';
    newItem.innerHTML = `
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">Valor *</label>
                <input type="text" name="configuracoes[${configuracaoIndex}][valor]" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Preço Adicional (R$)</label>
                <input type="number" name="configuracoes[${configuracaoIndex}][preco_adicional]" class="form-control" step="0.01" min="0">
            </div>
            <div class="col-md-4">
                <label class="form-label">Descrição</label>
                <input type="text" name="configuracoes[${configuracaoIndex}][descricao]" class="form-control">
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-danger btn-sm remover-configuracao">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(newItem);
    configuracaoIndex++;
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.remover-configuracao')) {
        e.preventDefault();
        const item = e.target.closest('.configuracao-item');
        if (document.querySelectorAll('.configuracao-item').length > 1) {
            item.remove();
        } else {
            alert('Deve haver pelo menos uma opção configurável.');
        }
    }
});
</script>
@endsection