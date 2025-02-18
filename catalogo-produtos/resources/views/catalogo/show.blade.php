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
                    <h2 class="produto-preco">R$ {{ number_format($produto->preco, 2, ',', '.') }}</h2>

                    <!-- Formulário de Adicionar ao Carrinho -->
                    <form action="{{ route('catalogo.adicionarCarrinho') }}" method="POST">
                        @csrf
                        <input type="hidden" name="produto_id" value="{{ $produto->id }}">
                        <div class="form-group">
                            <label for="quantidade">Quantidade:</label>
                            <input type="number" name="quantidade" id="quantidade" min="1" value="1" required>
                        </div>
                        <div class="d-flex gap-3 mt-4">
                        <button class="btn btn-primary add-to-cart" data-id="{{ $produto->id }}" data-nome="{{ $produto->nome }}" data-preco="{{ $produto->preco }}">
    Adicionar ao Carrinho
</button>
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
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                let produtoId = this.getAttribute('data-id');
                let nome = this.getAttribute('data-nome');
                let preco = this.getAttribute('data-preco');

                let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
                
                let produtoExistente = carrinho.find(produto => produto.id === produtoId);
                if (produtoExistente) {
                    produtoExistente.quantidade += 1;
                } else {
                    carrinho.push({ id: produtoId, nome, preco, quantidade: 1 });
                }

                localStorage.setItem('carrinho', JSON.stringify(carrinho));
                alert('Produto adicionado ao carrinho!');
            });
        });
    });
</script>