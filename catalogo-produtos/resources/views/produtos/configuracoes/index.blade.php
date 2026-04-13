@extends('layouts.main')

@section('content')
<div class="list-container">
    <h1 class="grid-tipo-entrada-title-container">Configurações de Produtos</h1>

    <div class="filtro-container">
        <p>Aqui você pode gerenciar as opções personalizáveis de cada produto.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @foreach($produtos as $produto)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ $produto->nome }}</h5>
                <small class="text-muted">{{ $produto->descricao }}</small>
            </div>
            <div class="card-body">
                @if($produto->opcoes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Categoria</th>
                                    <th>Nome da Opção</th>
                                    <th>Tipo</th>
                                    <th>Obrigatório</th>
                                    <th>Configurações</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($produto->opcoes as $opcao)
                                    <tr>
                                        <td><span class="badge bg-secondary">{{ $opcao->categoria }}</span></td>
                                        <td>{{ $opcao->nome }}</td>
                                        <td>
                                            @if($opcao->tipo == 'selecao_unica')
                                                Seleção Única
                                            @elseif($opcao->tipo == 'selecao_multipla')
                                                Seleção Múltipla
                                            @else
                                                Quantidade Fixa
                                            @endif
                                        </td>
                                        <td>
                                            @if($opcao->obrigatorio)
                                                <span class="badge bg-danger">Sim</span>
                                            @else
                                                <span class="badge bg-success">Não</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>
                                                @foreach($opcao->configuracoes as $config)
                                                    {{ $config->valor }}@if(!$loop->last), @endif
                                                @endforeach
                                            </small>
                                        </td>
                                        <td>
                                            <a href="{{ route('produtos.configuracoes.edit', [$produto->id, $opcao->id]) }}"
                                               class="btn btn-sm btn-warning">Editar</a>
                                            <form action="{{ route('produtos.configuracoes.destroy', [$produto->id, $opcao->id]) }}"
                                                  method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Tem certeza que deseja remover esta configuração?')">
                                                    Remover
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Este produto não possui configurações personalizáveis.</p>
                @endif

                <div class="mt-3">
                    <a href="{{ route('produtos.configuracoes.create', $produto->id) }}"
                       class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> Adicionar Configuração
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection