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
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">

    <!-- CSS Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS da aplicação -->
    <link rel="stylesheet" href="/css/style_site.css">

    <!-- Script do Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="collapse navbar-collapse" id="navbar">
            <a href="/" class="navbar-brand">
                <img src="/img/logo_lacasadedoces-removebg-preview.png" alt="lacasadedoces">
            </a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a href="/catalogo" class="nav-link">Catálogo</a>
                </li>
            </ul>
        </div>
    </nav>
</header>

<section class="produtos-carrinho-container">
    <div class="container mt-4">
        <h2>Meu Carrinho</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @php
            $itensCarrinho = session('carrinho', []);
        @endphp

        @if(empty($itensCarrinho))
            <p>Seu carrinho está vazio.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Preço</th>
                        <th>Total</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($itensCarrinho as $id => $item)
                        <tr>
                            <td>{{ $item['nome'] }}</td>
                            <td>
                                <form action="{{ route('carrinho.atualizar', $id) }}" method="POST">
                                    @csrf
                                    <input type="number" name="quantidade" value="{{ $item['quantidade'] }}" min="1">
                                    <button type="submit" class="btn btn-primary btn-sm">Atualizar</button>
                                </form>
                            </td>
                            <td>R$ {{ number_format($item['preco'], 2, ',', '.') }}</td>
                            <td>R$ {{ number_format($item['preco'] * $item['quantidade'], 2, ',', '.') }}</td>
                            <td>
                                <form action="{{ route('carrinho.remover', $id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Remover</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <a href="{{ route('pedido.formulario') }}" class="btn btn-success mt-3">Finalizar Pedido</a>
        @endif
    </div>
</section>

</body>
</html>
