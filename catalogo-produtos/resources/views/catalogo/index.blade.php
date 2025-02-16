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
                    <a href="#catalogo-section" class="nav-link">Catálogo</a>
                </li>
                <li class="nav-item">
                    <a href="/carrinho" class="nav-link">Carrinho</a>
                </li>
                <li class="nav-item">
                    <a href="#empresa-section" class="nav-link">Sobre Nós</a>
                </li>
                <li class="nav-item">
                    @if(auth()->check()) <!-- Verifica se o usuário está logado -->
                        <form action="/logout" method="POST" class="d-inline">
                            @csrf
                            <a href="/logout" class="nav-link"
                               onclick="event.preventDefault();
                               this.closest('form').submit();">Sair</a>
                        </form>
                    @else
                        <a href="/login" class="nav-link">Painel Administrativo</a> <!-- Link para a tela de login -->
                    @endif
                </li>
            </ul>
        </div>
    </nav>
</header>

<section id="inicio-section">
    <div class="inicio-carrossel">
        <div class="carrossel">
            <div class="carrossel-imagens">
                <img src="img/imagem1.jpg" alt="Imagem 1">
                <img src="img/imagem2.jpg" alt="Imagem 2">
                <img src="img/imagem3.jpg" alt="Imagem 3">
                <img src="img/imagem4.jpg" alt="Imagem 4">
                <img src="img/imagem5.jpg" alt="Imagem 5">
            </div>
        </div>
        <div class="inicio-texto">
            <h1>Cada mordida, um momento de felicidade!</h1>
            <p>Na La Casa de Doces, transformamos açúcar em sorrisos e sonhos em sabores inesquecíveis. Venha adoçar sua vida com a gente!🍭💛</p>
            <a href="#catalogo-section" class="btn btn-info">Descubra Nossos Produtos</a>                
        </div>
    </div>
</section>

<section id="catalogo-section">
    <div class="catalogo-header">
        <h3 class="titulo"><strong>Catálogo de Produtos</strong></h3>

        <div class="filtro-container">
            <form action="{{ route('catalogo.index') }}" method="GET" class="form-busca">
                <input type="text" name="search" placeholder="Buscar produto..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-secondary">Buscar</button>
            </form>
        </div>
    </div>

    @foreach($categorias as $categoria)
        @if($categoria->produtos->count() > 0)
            <div class="categoria">
                <h4 class="categoria-titulo">{{ $categoria->nome }}</h4>
                <div class="catalogo-container">
                    @foreach($categoria->produtos as $produto)
                        <div class="produto-card">
                            <img src="{{ asset('storage/' . $produto->imagem) }}" alt="{{ $produto->nome }}" class="produto-imagem">
                            <h4>{{ $produto->nome }}</h4>
                            <p>Preço: R$ {{ number_format($produto->preco, 2, ',', '.') }}</p>
                            <a href="{{ route('catalogo.show', $produto->id) }}" class="btn btn-info">Ver Detalhes</a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
</section>


<section id="empresa-section">
    <div class="empresa">
        <div class="empresa-sobre">
            <h1>Sobre a Empresa</h1>
            <p>
                Bem-vindo à Casa dos Doces, uma loja especializada em oferecer deliciosas opções de doces e salgados para todos os momentos especiais da sua vida. Com receitas cuidadosamente selecionadas e ingredientes de alta qualidade, nossa missão é proporcionar uma experiência única de sabor e carinho em cada mordida.
            </p>
        </div>
        <div class="empresa-missao">
            <h1>Missão</h1>
            <p>Nossa missão é transformar cada momento em uma celebração de sabores, oferecendo produtos frescos, deliciosos e preparados com o mais alto padrão de qualidade. Trabalhamos para garantir que nossos clientes experimentem o prazer de comer algo feito com paixão, buscando sempre surpreender com novidades e mantendo as receitas tradicionais que conquistam gerações.</p>
        </div>
        <div class="empresa-visao">
            <h1>Visão</h1>
            <p>Ser a loja preferida de doces e salgados, reconhecida pela excelência em qualidade e pelo atendimento acolhedor e personalizado. Queremos expandir nossa presença, levando nossos produtos a mais pessoas, mantendo a tradição e inovando constantemente para atender às expectativas dos nossos clientes com um cardápio diversificado e saboroso.</p>
        </div>
        <div class="empresa-valores">
            <h1>Valores</h1>
            <ul>
                <li>
                    <strong>Qualidade:</strong> Trabalhamos com ingredientes frescos e de alta qualidade para garantir que cada produto seja delicioso e atenda às expectativas dos nossos clientes.
                </li>
                <li>
                    <strong>Comprometimento:</strong> Cada doce, cada salgado é preparado com dedicação e carinho, com o objetivo de oferecer sempre a melhor experiência ao nosso cliente.
                </li>
                <li>
                    <strong>Atendimento acolhedor:</strong> Acreditamos que o atendimento é tão importante quanto a qualidade do produto. Nosso time está sempre pronto para oferecer um serviço gentil e atencioso.
                </li>
            </ul>
        </div>
    </div>
</section>

<section id="contato-section">
    <div class="titulo">
        <h3><strong>Entre em Contato</strong></h3>
    </div>
    
    <div class="contato-info">
        <p>Tem alguma dúvida ou quer fazer um pedido especial? Entre em contato conosco! Ficaremos felizes em atender você.</p>
        <ul>
            <li>
                <ion-icon name="call-outline"></ion-icon>
                <a href="tel:+5562993847722"><strong>Telefone:</strong> (11) 1234-5678</a>
            </li>
            <li>
                <ion-icon name="mail-outline"></ion-icon>
                <a href="mailto:andressarodrigues.est@gmail.com"><strong>Email:</strong> contato@lacasadedoces.com.br</a>
            </li>
            <li>
                <ion-icon name="location-outline"></ion-icon>
                <strong>Cidade:</strong> Aparecida de Goiânia - GO
            </li>
        </ul>
        
        <div class="redes-sociais">
            <a href="https://www.instagram.com/seuinstagram" target="_blank">
                <ion-icon name="logo-instagram"></ion-icon>
            </a>
            <a href="https://www.facebook.com/seufacebook" target="_blank">
                <ion-icon name="logo-facebook"></ion-icon>
            </a>
            <a href="https://wa.me/5562993847722" target="_blank">
                <ion-icon name="logo-whatsapp"></ion-icon>
            </a>
        </div>
    </div>
</section>


<footer class="mt-auto">
    <p>&copy; 2025 La Casa de Doces</p>
</footer>
