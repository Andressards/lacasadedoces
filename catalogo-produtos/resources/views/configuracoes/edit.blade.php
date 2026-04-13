@extends('layouts.main')

@section('content')
<div class="create-container">
    <div class="header-container">
        <h1 class="titulo-form">Editar Configuração - {{ $produto->nome }}</h1>
    </div>

    <form action="{{ route('produtos.configuracoes.update', [$produto->id, $opcao->id]) }}" method="POST" id="configuracao-form">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="categoria" class="form-label">Categoria *</label>
                    <select name="categoria" id="categoria" class="form-control" required>
                        <option value="">Selecione uma categoria</option>
                        <option value="bolo" {{ $opcao->categoria == 'bolo' ? 'selected' : '' }}>Bolo</option>
                        <option value="doces" {{ $opcao->categoria == 'doces' ? 'selected' : '' }}>Doces</option>
                        <option value="salgados" {{ $opcao->categoria == 'salgados' ? 'selected' : '' }}>Salgados</option>
                        <option value="bebidas" {{ $opcao->categoria == 'bebidas' ? 'selected' : '' }}>Bebidas</option>
                        <option value="outros" {{ $opcao->categoria == 'outros' ? 'selected' : '' }}>Outros</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="tipo" class="form-label">Tipo de Seleção *</label>
                    <select name="tipo" id="tipo" class="form-control" required>
                        <option value="">Selecione o tipo</option>
                        <option value="selecao_unica" {{ $opcao->tipo == 'selecao_unica' ? 'selected' : '' }}>Seleção Única (cliente escolhe 1 opção)</option>
                        <option value="selecao_multipla" {{ $opcao->tipo == 'selecao_multipla' ? 'selected' : '' }}>Seleção Múltipla (cliente escolhe várias opções)</option>
                        <option value="quantidade_fixa" {{ $opcao->tipo == 'quantidade_fixa' ? 'selected' : '' }}>Quantidade Fixa (itens pré-definidos)</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="nome" class="form-label">Nome da Opção *</label>
            <input type="text" name="nome" id="nome" class="form-control" required
                   value="{{ $opcao->nome }}" placeholder="Ex: Sabor da Massa, Sabores dos Doces">
        </div>

        <div class="row" id="quantidade-container" style="{{ $opcao->tipo == 'selecao_multipla' ? '' : 'display: none;' }}">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="quantidade_minima" class="form-label">Quantidade Mínima</label>
                    <input type="number" name="quantidade_minima" id="quantidade_minima" class="form-control" min="1"
                           value="{{ $opcao->quantidade_minima }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="quantidade_maxima" class="form-label">Quantidade Máxima</label>
                    <input type="number" name="quantidade_maxima" id="quantidade_maxima" class="form-control" min="1"
                           value="{{ $opcao->quantidade_maxima }}">
                </div>
            </div>
        </div>

        <div class="mb-3" id="quantidade-fixa-container" style="{{ $opcao->tipo == 'quantidade_fixa' ? '' : 'display: none;' }}">
            <label for="quantidade_fixa" class="form-label">Quantidade Fixa</label>
            <input type="number" name="quantidade_fixa" id="quantidade_fixa" class="form-control" min="1"
                   value="{{ $opcao->quantidade_minima == $opcao->quantidade_maxima ? $opcao->quantidade_minima : '' }}"
                   placeholder="Ex: 5 (quantidade obrigatória)">
        </div>

        <div class="mb-3">
            <div class="form-check">
                <input type="checkbox" name="obrigatorio" id="obrigatorio" class="form-check-input" {{ $opcao->obrigatorio ? 'checked' : '' }}>
                <label for="obrigatorio" class="form-check-label">Opção obrigatória</label>
            </div>
        </div>

        <h4>Opções Disponíveis</h4>
        <div id="configuracoes-container">
            @foreach($opcao->configuracoes as $index => $config)
            <div class="configuracao-item border p-3 mb-3 rounded">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Valor *</label>
                        <input type="text" name="configuracoes[{{ $index }}][valor]" class="form-control" required
                               value="{{ $config->valor }}" placeholder="Ex: Chocolate, Brigadeiro">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Preço Adicional (R$)</label>
                        <input type="number" name="configuracoes[{{ $index }}][preco_adicional]" class="form-control" step="0.01" min="0"
                               value="{{ $config->preco_adicional }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Descrição</label>
                        <input type="text" name="configuracoes[{{ $index }}][descricao]" class="form-control"
                               value="{{ $config->descricao }}" placeholder="Descrição opcional">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-sm remover-configuracao">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mb-3">
            <button type="button" class="btn btn-secondary" id="adicionar-configuracao">
                <i class="bi bi-plus-lg"></i> Adicionar Opção
            </button>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Atualizar Configuração</button>
            <a href="{{ route('produtos.configuracoes.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<script>
document.getElementById('tipo').addEventListener('change', function() {
    const quantidadeContainer = document.getElementById('quantidade-container');
    const quantidadeFixaContainer = document.getElementById('quantidade-fixa-container');
    if (this.value === 'selecao_multipla') {
        quantidadeContainer.style.display = 'block';
        quantidadeFixaContainer.style.display = 'none';
    } else if (this.value === 'quantidade_fixa') {
        quantidadeContainer.style.display = 'none';
        quantidadeFixaContainer.style.display = 'block';
    } else {
        quantidadeContainer.style.display = 'none';
        quantidadeFixaContainer.style.display = 'none';
    }
});

let configuracaoIndex = {{ $opcao->configuracoes->count() }};

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