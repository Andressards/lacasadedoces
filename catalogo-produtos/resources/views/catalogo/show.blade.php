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
                    <li class="nav-item">
                        <a href="/categorias" class="nav-link">Entrar</a>
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

                    <!-- Opções Configuráveis -->
                    @if($produto->opcoes->count() > 0)
                        <div class="produto-opcoes mb-4">
                            <h4 class="mb-3">Personalize seu pedido:</h4>

                            @foreach($produto->opcoes->sortBy('ordem') as $opcao)
                                <div class="opcao-group mb-3 p-3 border rounded">
                                    <h5 class="opcao-nome">
                                        {{ $opcao->nome }}
                                        @if($opcao->obrigatorio)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </h5>

                                    @if($opcao->tipo == 'selecao_unica')
                                        <!-- Seleção Única -->
                                        <div class="opcao-selecao-unica">
                                            @foreach($opcao->configuracoes->sortBy('ordem') as $config)
                                                <div class="form-check">
                                                    <input class="form-check-input opcao-radio" type="radio"
                                                           name="opcoes[{{ $opcao->id }}]"
                                                           value="{{ $config->id }}"
                                                           data-preco-adicional="{{ $config->preco_adicional }}"
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
                                        <div class="opcao-selecao-multipla">
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
                                                           data-opcao-id="{{ $opcao->id }}">
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

                    <!-- Formulário de Adicionar ao Carrinho -->
                    <form action="{{ route('catalogo.adicionarCarrinho') }}" method="POST">
                        @csrf
                        <input type="hidden" name="produto_id" value="{{ $produto->id }}">
                        <div class="form-group">
                            <label for="quantidade">Quantidade:</label>
                            <input type="number" name="quantidade" id="quantidade" min="1" value="1" required>
                        </div>
                        <div class="d-flex gap-3 mt-4">
                            @if($produto->opcoes->count() > 0)
                                <!-- Produto com configurações - usar JavaScript -->
                                <button class="btn btn-primary add-to-cart" data-id="{{ $produto->id }}" data-nome="{{ $produto->nome }}" data-preco="{{ $produto->preco }}">
                                    Adicionar ao Carrinho
                                </button>
                            @else
                                <!-- Produto sem configurações - usar formulário normal -->
                                <button type="submit" class="btn btn-primary">
                                    Adicionar ao Carrinho
                                </button>
                            @endif
                            <a href="/catalogo" class="btn btn-outline-secondary btn-lg">Voltar ao Catálogo</a>
                        </div>
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">
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
    // Preço base do produto
    const precoBase = {{ $produto->preco }};
    const precoTotalElement = document.getElementById('preco-total');

    // Função para calcular o preço total
    function calcularPrecoTotal() {
        let precoTotal = precoBase;

        // Somar preços adicionais das opções selecionadas
        document.querySelectorAll('.opcao-radio:checked, .opcao-checkbox:checked').forEach(input => {
            const precoAdicional = parseFloat(input.getAttribute('data-preco-adicional')) || 0;
            precoTotal += precoAdicional;
        });

        // Multiplicar pela quantidade
        const quantidade = parseInt(document.getElementById('quantidade').value) || 1;
        precoTotal *= quantidade;

        precoTotalElement.textContent = 'R$ ' + precoTotal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Validar seleções múltiplas
    function validarSelecaoMultipla() {
        @foreach($produto->opcoes->where('tipo', 'selecao_multipla') as $opcao)
            const checkboxes = document.querySelectorAll(`input[name="opcoes[{{ $opcao->id }}][]"]:checked`);
            const min = {{ $opcao->quantidade_minima }};
            const max = {{ $opcao->quantidade_maxima }};

            if (checkboxes.length < min || checkboxes.length > max) {
                return false;
            }
        @endforeach
        return true;
    }

    // Event listeners para atualizar preço
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('opcao-radio') || e.target.classList.contains('opcao-checkbox')) {
            calcularPrecoTotal();
        }
    });

    document.getElementById('quantidade').addEventListener('input', calcularPrecoTotal);

    // Modificar o botão de adicionar ao carrinho
    document.querySelector('.add-to-cart').addEventListener('click', function(e) {
        e.preventDefault();

        // Validar opções obrigatórias
        const opcoesObrigatorias = document.querySelectorAll('.opcao-radio[required]');
        let opcoesValidas = true;

        opcoesObrigatorias.forEach(radio => {
            const groupName = radio.name;
            const checked = document.querySelector(`input[name="${groupName}"]:checked`);
            if (!checked) {
                opcoesValidas = false;
            }
        });

        // Validar seleções múltiplas
        if (!validarSelecaoMultipla()) {
            opcoesValidas = false;
        }

        if (!opcoesValidas) {
            alert('Por favor, selecione todas as opções obrigatórias e respeite os limites de seleção.');
            return;
        }

        // Coletar configurações selecionadas
        const configuracoesSelecionadas = {};

        // Coletar seleções únicas
        document.querySelectorAll('.opcao-radio:checked').forEach(radio => {
            const opcaoId = radio.name.match(/opcoes\[(\d+)\]/)[1];
            configuracoesSelecionadas[opcaoId] = [radio.value];
        });

        // Coletar seleções múltiplas
        document.querySelectorAll('.opcao-checkbox:checked').forEach(checkbox => {
            const opcaoId = checkbox.getAttribute('data-opcao-id');
            if (!configuracoesSelecionadas[opcaoId]) {
                configuracoesSelecionadas[opcaoId] = [];
            }
            configuracoesSelecionadas[opcaoId].push(checkbox.value);
        });

        // Preparar dados do produto
        const produtoId = this.getAttribute('data-id');
        const nome = this.getAttribute('data-nome');
        const preco = precoBase;
        const quantidade = parseInt(document.getElementById('quantidade').value) || 1;

        // Calcular preço total das opções
        let precoOpcoes = 0;
        Object.values(configuracoesSelecionadas).flat().forEach(configId => {
            const input = document.querySelector(`input[value="${configId}"]`);
            if (input) {
                precoOpcoes += parseFloat(input.getAttribute('data-preco-adicional')) || 0;
            }
        });

        const precoTotal = (preco + precoOpcoes) * quantidade;

        // Adicionar ao carrinho (localStorage)
        let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
        const itemCarrinho = {
            id: produtoId,
            nome: nome,
            preco: preco,
            preco_opcoes: precoOpcoes,
            quantidade: quantidade,
            configuracoes: configuracoesSelecionadas,
            preco_total: precoTotal
        };

        carrinho.push(itemCarrinho);
        localStorage.setItem('carrinho', JSON.stringify(carrinho));

        alert('Produto personalizado adicionado ao carrinho!');
    });

    // Calcular preço inicial
    document.addEventListener('DOMContentLoaded', calcularPrecoTotal);
</script>