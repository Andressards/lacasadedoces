<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>LaCasaDeDoces</title>

    <!-- Fonte do Google -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <!-- CSS Boostrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- CSS da aplicação -->
    <link rel="stylesheet" href="/css/style_site.css">

    <!-- Script do Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a href="/" class="navbar-brand">
                <img src="/img/logo_lacasadedoces-removebg-preview.png" alt="lacasadedoces">
            </a>

            <!-- Botão Hamburguer -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="/carrinho" class="nav-link">Carrinho</a>
                    </li>
                    <li class="nav-item">
                        <a href="/catalogo" class="nav-link">Catálogo</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<section class="produto-detalhes-container">
    <div class="card produto-detalhe-card">
        <div class="row g-0">
            <div class="col-md-6">
                <img src="{{ asset('storage/' . $produto->imagem) }}" alt="{{ $produto->nome }}" class="produto-imagem">
            </div>
            <div class="col-md-6">
                <div class="card-body">
                    <h1 class="produto-nome">{{ $produto->nome }}</h1>
                    <p class="produto-descricao"><strong>Descrição:</strong> {{ $produto->descricao }}</p>

                    <!-- Formulário de Adicionar ao Carrinho -->
                    <form id="form-adicionar-carrinho" action="{{ route('catalogo.adicionarCarrinho') }}" method="POST">
                        @csrf
                        <input type="hidden" name="produto_id" value="{{ $produto->id }}">

                        <!-- Opções Configuráveis -->
                        @if($produto->opcoes->count() > 0)
                            <div class="produto-opcoes mb-4">
                                <h4 class="mb-3">Personalize seu pedido:</h4>

                                @foreach($produto->opcoes->sortBy('ordem') as $opcao)
                                    <div class="opcao-group mb-3 p-3 border rounded {{ $errors->has('opcoes.' . $opcao->id) ? 'border-danger' : '' }}" id="opcao-group-{{ $opcao->id }}">
                                        <h5 class="opcao-nome">
                                            {{ $opcao->nome }}
                                            @if($opcao->obrigatorio)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </h5>

                                        <!-- Container para mensagens de erro (Backend e Frontend) -->
                                        <div class="error-message text-danger small mb-2" id="error-{{ $opcao->id }}">
                                            @if($errors->has('opcoes.' . $opcao->id))
                                                {{ $errors->first('opcoes.' . $opcao->id) }}
                                            @endif
                                        </div>

                                        @if($opcao->tipo == 'selecao_unica')
                                            <!-- Seleção Única -->
                                            <div class="opcao-selecao-unica">
                                                @foreach($opcao->configuracoes->sortBy('ordem') as $config)
                                                    <div class="form-check">
                                                        <input class="form-check-input opcao-radio" type="radio"
                                                               name="opcoes[{{ $opcao->id }}]"
                                                               value="{{ $config->id }}"
                                                               data-preco-adicional="{{ $config->preco_adicional }}"
                                                               data-opcao-id="{{ $opcao->id }}"
                                                               {{ (old("opcoes.{$opcao->id}") == $config->id) ? 'checked' : '' }}
                                                               {{ $opcao->obrigatorio ? 'required' : '' }}>
                                                        <label class="form-check-label">
                                                            {{ $config->valor }}
                                                            @if($config->preco_adicional > 0)
                                                                <small class="text-success">(+R$ {{ number_format($config->preco_adicional, 2, ',', '.') }})</small>
                                                            @endif
                                                            @if($config->descricao)
                                                                <br><small class="text-muted">{{ $config->descricao }}</small>
                                                            @endif
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>

                                        @elseif($opcao->tipo == 'selecao_multipla')
                                            <!-- Seleção Múltipla -->
                                            <div class="opcao-selecao-multipla" 
                                                 data-min="{{ $opcao->quantidade_minima }}" 
                                                 data-max="{{ $opcao->quantidade_maxima }}"
                                                 data-nome="{{ $opcao->nome }}"
                                                 data-opcao-id="{{ $opcao->id }}">
                                                <small class="text-muted mb-2 d-block">
                                                    Selecione entre {{ $opcao->quantidade_minima }} e {{ $opcao->quantidade_maxima }} opções
                                                </small>
                                                @foreach($opcao->configuracoes->sortBy('ordem') as $config)
                                                    <div class="form-check">
                                                        <input class="form-check-input opcao-checkbox"
                                                               type="checkbox"
                                                               name="opcoes[{{ $opcao->id }}][]"
                                                               value="{{ $config->id }}"
                                                               data-preco-adicional="{{ $config->preco_adicional }}"
                                                               data-opcao-id="{{ $opcao->id }}"
                                                               {{ (is_array(old("opcoes.{$opcao->id}")) && in_array($config->id, old("opcoes.{$opcao->id}"))) ? 'checked' : '' }}>
                                                        <label class="form-check-label">
                                                            {{ $config->valor }}
                                                            @if($config->preco_adicional > 0)
                                                                <small class="text-success">(+R$ {{ number_format($config->preco_adicional, 2, ',', '.') }})</small>
                                                            @endif
                                                            @if($config->descricao)
                                                                <br><small class="text-muted">{{ $config->descricao }}</small>
                                                            @endif
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>

                                        @else
                                            <!-- Quantidade Fixa - mostra apenas informativo -->
                                            <div class="opcao-quantidade-fixa">
                                                <p class="mb-1">Inclui:</p>
                                                <ul class="list-unstyled">
                                                    @foreach($opcao->configuracoes->sortBy('ordem') as $config)
                                                        <li>
                                                            {{ $config->valor }}
                                                            @if($config->descricao)
                                                                <small class="text-muted"> - {{ $config->descricao }}</small>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <h2 class="produto-preco" id="preco-total">R$ {{ number_format($produto->preco, 2, ',', '.') }}</h2>

                        <div class="form-group">
                            <label for="quantidade">Quantidade:</label>
                            <input type="number" name="quantidade" id="quantidade" min="1" value="{{ old('quantidade', 1) }}" required class="form-control w-25">
                        </div>

                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Adicionar ao Carrinho
                            </button>
                            <a href="/catalogo" class="btn btn-outline-secondary btn-lg">Voltar ao Catálogo</a>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success mt-3">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger mt-3">
                                {{ session('error') }}
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const precoBase = {{ $produto->preco }};
    const precoTotalElement = document.getElementById('preco-total');
    const form = document.getElementById('form-adicionar-carrinho');

    function calcularPrecoTotal() {
        let precoTotal = precoBase;
        const quantidade = parseInt(document.getElementById('quantidade').value) || 1;

        document.querySelectorAll('.opcao-radio:checked, .opcao-checkbox:checked').forEach(input => {
            const precoAdicional = parseFloat(input.getAttribute('data-preco-adicional')) || 0;
            precoTotal += precoAdicional;
        });

        precoTotal *= quantidade;
        precoTotalElement.textContent = 'R$ ' + precoTotal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Event listeners para atualizar preço e limpar erros ao interagir
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('opcao-radio') || e.target.classList.contains('opcao-checkbox')) {
            calcularPrecoTotal();
            
            // Limpar erro visual ao selecionar
            const opcaoId = e.target.getAttribute('data-opcao-id');
            const group = document.getElementById(`opcao-group-${opcaoId}`);
            const errorDiv = document.getElementById(`error-${opcaoId}`);
            if (group) group.classList.remove('border-danger');
            if (errorDiv) errorDiv.textContent = '';
        }
    });

    document.getElementById('quantidade').addEventListener('input', calcularPrecoTotal);

    // Validação antes de enviar (Frontend sem alert)
    form.addEventListener('submit', function(e) {
        let valido = true;

        // Limpar erros anteriores
        document.querySelectorAll('.opcao-group').forEach(g => g.classList.remove('border-danger'));
        document.querySelectorAll('.error-message').forEach(m => m.textContent = '');

        // Validar seleções múltiplas
        document.querySelectorAll('.opcao-selecao-multipla').forEach(container => {
            const min = parseInt(container.getAttribute('data-min'));
            const max = parseInt(container.getAttribute('data-max'));
            const nome = container.getAttribute('data-nome');
            const opcaoId = container.getAttribute('data-opcao-id');
            const selecionados = container.querySelectorAll('input[type="checkbox"]:checked').length;

            if (selecionados < min) {
                valido = false;
                document.getElementById(`opcao-group-${opcaoId}`).classList.add('border-danger');
                document.getElementById(`error-${opcaoId}`).textContent = `A opção "${nome}" requer no mínimo ${min} seleções.`;
            } else if (selecionados > max) {
                valido = false;
                document.getElementById(`opcao-group-${opcaoId}`).classList.add('border-danger');
                document.getElementById(`error-${opcaoId}`).textContent = `A opção "${nome}" permite no máximo ${max} seleções.`;
            }
        });

        // Validar seleções únicas obrigatórias (caso o required do HTML falhe ou seja removido)
        document.querySelectorAll('.opcao-selecao-unica').forEach(container => {
            const radio = container.querySelector('input[type="radio"]');
            if (radio && radio.hasAttribute('required')) {
                const opcaoId = radio.getAttribute('data-opcao-id');
                const selecionado = container.querySelector('input[type="radio"]:checked');
                if (!selecionado) {
                    valido = false;
                    document.getElementById(`opcao-group-${opcaoId}`).classList.add('border-danger');
                    document.getElementById(`error-${opcaoId}`).textContent = `Esta opção é obrigatória.`;
                }
            }
        });

        if (!valido) {
            e.preventDefault();
            // Scroll suave até o primeiro erro
            const primeiroErro = document.querySelector('.border-danger');
            if (primeiroErro) {
                primeiroErro.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });

    // Calcular preço inicial
    document.addEventListener('DOMContentLoaded', calcularPrecoTotal);
</script>

</body>
</html>