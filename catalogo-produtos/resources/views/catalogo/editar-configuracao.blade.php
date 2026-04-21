<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>LaCasaDeDoces - Editar Configuração</title>

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
                    <h1 class="produto-nome">Editar Configuração: {{ $produto->nome }}</h1>
                    <p class="produto-descricao"><strong>Descrição:</strong> {{ $produto->descricao }}</p>

                    <!-- Formulário de Atualizar Configuração -->
                    <form id="form-atualizar-configuracao" action="{{ route('carrinho.atualizarConfiguracao', $id) }}" method="POST">
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
                                                    @php
                                                        $isChecked = isset($item['opcoes'][$opcao->id]) && $item['opcoes'][$opcao->id] == $config->id;
                                                    @endphp
                                                    <div class="form-check">
                                                        <input class="form-check-input opcao-radio" type="radio"
                                                               name="opcoes[{{ $opcao->id }}]"
                                                               value="{{ $config->id }}"
                                                               data-preco-adicional="{{ $config->preco_adicional }}"
                                                               data-opcao-id="{{ $opcao->id }}"
                                                               {{ $isChecked ? 'checked' : '' }}
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
                                                    @php
                                                        $isChecked = isset($item['opcoes'][$opcao->id]) && is_array($item['opcoes'][$opcao->id]) && in_array($config->id, $item['opcoes'][$opcao->id]);
                                                    @endphp
                                                    <div class="form-check">
                                                        <input class="form-check-input opcao-checkbox"
                                                               type="checkbox"
                                                               name="opcoes[{{ $opcao->id }}][]"
                                                               value="{{ $config->id }}"
                                                               data-preco-adicional="{{ $config->preco_adicional }}"
                                                               data-opcao-id="{{ $opcao->id }}"
                                                               {{ $isChecked ? 'checked' : '' }}>
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

                        <h2 class="produto-preco" id="preco-total">R$ {{ number_format($produto->preco + ($item['preco_adicional'] ?? 0), 2, ',', '.') }}</h2>

                        <div class="form-group">
                            <label for="quantidade">Quantidade:</label>
                            <input type="number" name="quantidade" id="quantidade" min="1" value="{{ $item['quantidade'] }}" required class="form-control w-25">
                        </div>

                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Atualizar Configuração
                            </button>
                            <a href="{{ route('pedido.formulario') }}" class="btn btn-outline-secondary btn-lg">Voltar ao Finalizar Pedido</a>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success mt-3">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger mt-3">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
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
    const form = document.getElementById('form-atualizar-configuracao');

    function calcularPrecoTotal() {
        let precoTotal = precoBase;
        const quantidade = parseInt(document.getElementById('quantidade').value) || 1;

        document.querySelectorAll('.opcao-radio:checked, .opcao-checkbox:checked').forEach(input => {
            const precoAdicional = parseFloat(input.getAttribute('data-preco-adicional')) || 0;
            precoTotal += precoAdicional;
        });

        precoTotal *= quantidade;
        precoTotalElement.textContent = 'R$ ' + precoTotal.toFixed(2).replace('.', ',');
    }

    // Calcular preço inicial
    calcularPrecoTotal();

    // Recalcular quando opções mudam
    document.addEventListener('change', function(event) {
        if (event.target.classList.contains('opcao-radio') || event.target.classList.contains('opcao-checkbox')) {
            calcularPrecoTotal();
        }
    });

    // Recalcular quando quantidade muda
    document.getElementById('quantidade').addEventListener('input', calcularPrecoTotal);

    // Validação frontend para seleção múltipla
    form.addEventListener('submit', function(event) {
        let hasError = false;

        document.querySelectorAll('.opcao-selecao-multipla').forEach(container => {
            const opcaoId = container.getAttribute('data-opcao-id');
            const min = parseInt(container.getAttribute('data-min'));
            const max = parseInt(container.getAttribute('data-max'));
            const nome = container.getAttribute('data-nome');
            const checked = container.querySelectorAll('input[type="checkbox"]:checked').length;

            const errorDiv = document.getElementById('error-' + opcaoId);
            errorDiv.textContent = '';

            if (checked < min || checked > max) {
                errorDiv.textContent = `Selecione entre ${min} e ${max} opções para ${nome}.`;
                hasError = true;
            }
        });

        if (hasError) {
            event.preventDefault();
        }
    });
</script>

</body>
</html>