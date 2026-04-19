<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\PedidoItemConfiguracao;
use App\Models\ProdutoConfiguracao;
use App\Models\ItemCarrinho;
use App\Models\Produtos;
use Illuminate\Http\Request;


class PedidoController extends Controller
{
    public function formularioPedido()
    {
        // Pegando itens do carrinho da sessão
        $itensCarrinho = session('carrinho', []);

        if (empty($itensCarrinho)) {
            return redirect()->route('catalogo.carrinho')->with('error', 'Seu carrinho está vazio.');
        }

        return view('pedidos.finalizar', compact('itensCarrinho'));
    }

    public function salvarPedido(Request $request)
    {
        $request->validate([
            'nome_cliente' => 'required|string|max:255',
            'tipo_entrega' => 'required|in:entrega,retirada',
            'data_entrega' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $minTime = strtotime('+3 hours'); // Hora atual + 3 horas
                    if (strtotime($value) < $minTime) {
                        $fail('A data de entrega deve ser pelo menos 3 horas após o horário atual.');
                    }
                },
            ],
        ]);

        if ($request->tipo_entrega === 'entrega') {
            $request->validate([
                'rua' => 'required|string|max:255',
                'bairro' => 'required|string|max:255',
                'numero' => 'required|string|max:10',
                'cep' => 'required|numeric',
            ]);
        }

        // Obtém os itens do carrinho na sessão
        $itensCarrinho = session('carrinho', []);

        if (empty($itensCarrinho)) {
            return redirect()->route('catalogo.carrinho')->with('error', 'Seu carrinho está vazio.');
        }

        // Calcular o total do pedido
        $totalPedido = collect($itensCarrinho)->sum(function ($item) {
            $precoUnitario = $item['preco'] + ($item['preco_adicional'] ?? 0);
            return $item['quantidade'] * $precoUnitario;
        });

        // Mapeia os itens para formato correto
        $itensPedido = collect($itensCarrinho)->map(function ($item) {
            $precoUnitario = $item['preco'] + ($item['preco_adicional'] ?? 0);
            $configuracoes = [];
            
            if (isset($item['opcoes']) && !empty($item['opcoes'])) {
                foreach ($item['opcoes'] as $opcaoId => $configIds) {
                    $configIds = is_array($configIds) ? $configIds : [$configIds];
                    foreach ($configIds as $configId) {
                        $configuracao = \App\Models\ProdutoConfiguracao::find($configId);
                        if ($configuracao) {
                            $configuracoes[] = [
                                'opcao' => $configuracao->produtoOpcao->nome,
                                'configuracao' => $configuracao->valor,
                                'preco_adicional' => $configuracao->preco_adicional
                            ];
                        }
                    }
                }
            }
            
            return [
                'produto' => $item['nome'],
                'quantidade' => $item['quantidade'],
                'preco_unitario' => $precoUnitario,
                'configuracoes' => $configuracoes
            ];
        })->toArray();

        // Criar pedido
        $pedido = Pedido::create([
            'nome_cliente' => $request->nome_cliente,
            'tipo_entrega' => $request->tipo_entrega,
            'data_entrega' => $request->data_entrega,
            'observacao' => $request->observacao,
            'rua' => $request->rua,
            'bairro' => $request->bairro,
            'numero' => $request->numero,
            'quadra' => $request->quadra,
            'lote' => $request->lote,
            'numero_contato' => $request->numero_contato,
            'itens_pedido' => $itensPedido,
            'status' => 'pendente',
        ]);

        // Criar os itens do pedido na tabela pedido_itens
        foreach ($itensCarrinho as $item) {
            $precoUnitario = $item['preco'] + ($item['preco_adicional'] ?? 0);
            
            $pedidoItem = PedidoItem::create([
                'pedido_id' => $pedido->id,
                'produto_id' => $item['id'],
                'quantidade' => $item['quantidade'],
                'preco_unitario' => $precoUnitario,
            ]);
            
            // Criar configurações do item se existirem
            if (isset($item['opcoes']) && !empty($item['opcoes'])) {
                foreach ($item['opcoes'] as $opcaoId => $configIds) {
                    $configIds = is_array($configIds) ? $configIds : [$configIds];
                    foreach ($configIds as $configId) {
                        $configuracao = \App\Models\ProdutoConfiguracao::find($configId);
                        if ($configuracao) {
                            PedidoItemConfiguracao::create([
                                'pedido_item_id' => $pedidoItem->id,
                                'produto_opcao_id' => $configuracao->produto_opcao_id,
                                'produto_configuracao_id' => $configId,
                            ]);
                        }
                    }
                }
            }
        }

        // Limpar o carrinho da sessão após finalizar pedido
        session()->forget('carrinho');

        // Montar mensagem para WhatsApp
        $numeroWhatsApp = '5562993847722';

        $mensagem = "Olá, gostaria de fazer um pedido!\n"
            . "\nCliente: {$pedido->nome_cliente}\n"
            . "Data de Entrega: {$pedido->data_entrega}\n"
            . "Tipo de Entrega: " . ucfirst($pedido->tipo_entrega) . "\n"
            . "Total: R$ " . number_format($totalPedido, 2, ',', '.') . "\n"
            . "Itens do Pedido:\n";

        foreach ($itensPedido as $item) {
            $mensagem .= "- {$item['produto']} (x{$item['quantidade']}) - R$ " . number_format($item['preco_unitario'], 2, ',', '.') . "\n";
            
            if (!empty($item['configuracoes'])) {
                // Group configurations by option
                $configuracoesPorOpcao = [];
                foreach ($item['configuracoes'] as $config) {
                    $opcaoNome = $config['opcao'];
                    if (!isset($configuracoesPorOpcao[$opcaoNome])) {
                        $configuracoesPorOpcao[$opcaoNome] = [];
                    }
                    $configuracoesPorOpcao[$opcaoNome][] = $config;
                }
                
                foreach ($configuracoesPorOpcao as $opcaoNome => $configs) {
                    $mensagem .= "  └ {$opcaoNome}: ";
                    $configuracaoTexts = [];
                    foreach ($configs as $config) {
                        $text = $config['configuracao'];
                        if ($config['preco_adicional'] > 0) {
                            $text .= " (+R$ " . number_format($config['preco_adicional'], 2, ',', '.') . ")";
                        }
                        $configuracaoTexts[] = $text;
                    }
                    $mensagem .= implode(', ', $configuracaoTexts) . "\n";
                }
            }
        }

        if (!empty($pedido->observacao)) {
            $mensagem .= "\nObservação: {$pedido->observacao}\n";
        }

        // Adicionar endereço se for entrega
        if ($pedido->tipo_entrega === 'entrega') {
            $mensagem .= "\nEndereço de Entrega:\n";
            $mensagem .= "{$pedido->rua}, Nº {$pedido->numero}, Bairro {$pedido->bairro}\n";
            if ($pedido->quadra) $mensagem .= "Quadra: {$pedido->quadra} ";
            if ($pedido->lote) $mensagem .= "Lote: {$pedido->lote} ";
            if ($pedido->cep) $mensagem .= "\nCEP: {$pedido->cep}";
            $mensagem .= "\n Valor da Entrega: a combinar\n";
            $mensagem .= "\n Aguardo a confirmação! Obrigado(a).";
        }

        $mensagem = mb_convert_encoding($mensagem, 'UTF-8', 'auto');
        $linkWhatsApp = "https://wa.me/{$numeroWhatsApp}?text=" . rawurlencode($mensagem);


        return redirect()->away($linkWhatsApp);
    }


    public function cancelar($id)
    {
        $pedido = Pedido::findOrFail($id);
        $pedido->status = 'Cancelado'; // Define o status como Cancelado
        $pedido->save();

        return redirect()->route('pedidos.gerenciar')->with('msg', 'Pedido cancelado com sucesso!');
    }

    public function finalizar($id)
    {
        $pedido = Pedido::findOrFail($id);
        $pedido->status = 'Finalizado'; // Define o status como Finalizado
        $pedido->save();

        return redirect()->route('pedidos.gerenciar')->with('msg', 'Pedido cancelado com sucesso!');
    }

    public function ativar($id)
    {
        $pedido = Pedido::findOrFail($id);
        $pedido->status = 'pendente'; // Define o status como Cancelado
        $pedido->save();

        return redirect()->route('pedidos.historico')->with('msg', 'Pedido ativado com sucesso!');
    }

    // Método para gerenciar pedidos
    public function gerenciar()
    {
        // Buscar pedidos com status 'pendente' ou 'em andamento'
        $pedidos = Pedido::whereIn('status', ['pendente', 'em andamento'])->get();
        
        // Retornar a view com a lista de pedidos
        return view('pedidos.gerenciar', compact('pedidos'));
    }

    public function editar($id)
    {
        // Busca o pedido pelo ID com seus itens, configurações e produtos relacionados
        $pedido = Pedido::with('itens.produto', 'itens.configuracoes.produtoConfiguracao')->findOrFail($id);
        
        // Busca todos os produtos disponíveis para o dropdown de edição
        $produtos = Produtos::where('ativo', true)->with('opcoes.configuracoes')->get();

        // Retorna a view de edição com os dados do pedido
        return view('pedidos.edit', compact('pedido', 'produtos'));
    }

    // Método para visualizar histórico de pedidos
    public function historico()
    {
        // Buscar pedidos com status 'pendente' ou 'em andamento'
        $pedidos = Pedido::whereNotIn('status', ['pendente', 'em andamento'])->get();
        
        // Retornar a view com a lista de pedidos
        return view('pedidos.historico', compact('pedidos'));
    }

    public function criarPedido()
    {
        $produtos = ProdutosController::all(); // Busca todos os produtos do banco
        return view('pedidos.create', compact('produtos'));
    }

    public function criarPedidos(Request $request)
    {
        $pedido = Pedido::create([
            'nome_cliente' => $request->nome_cliente,
            'data_entrega' => $request->data_entrega,
            'numero_contato' => $request->numero_contato,
            'observacao' => $request->observacao ?? null,
            'status' => 'pendente',
        ]);

        foreach ($request->itens as $item) {
            $produto = Produtos::find($item['produto_id']);
            PedidoItem::create([
                'pedido_id' => $pedido->id,
                'produto_id' => $item['produto_id'],
                'quantidade' => $item['quantidade'],
                'preco_unitario' => $produto->preco,
            ]);
        }

        return redirect()->route('pedidos.index')->with('success', 'Pedido criado com sucesso!');
    }

    public function formularioCriar()
    {
        return view('pedidos.create');
    }

    public function atualizar(Request $request, $id)
    {
        $pedido = Pedido::findOrFail($id);

        // Validar dados do pedido
        $request->validate([
            'nome_cliente' => 'required|string|max:255',
            'tipo_entrega' => 'required|in:entrega,retirada',
            'data_entrega' => 'required|date',
            'numero_contato' => 'nullable|string|max:20',
            'observacao' => 'nullable|string',
            'rua' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:10',
            'bairro' => 'nullable|string|max:255',
            'quadra' => 'nullable|numeric',
            'lote' => 'nullable|numeric',
            'cep' => 'nullable|numeric',
            'itens' => 'required|array|min:1',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.quantidade' => 'required|numeric|min:1',
        ]);

        // Atualizar dados do pedido
        $pedido->update([
            'nome_cliente' => $request->nome_cliente,
            'tipo_entrega' => $request->tipo_entrega,
            'data_entrega' => $request->data_entrega,
            'numero_contato' => $request->numero_contato,
            'observacao' => $request->observacao,
            'rua' => $request->rua,
            'numero' => $request->numero,
            'bairro' => $request->bairro,
            'quadra' => $request->quadra,
            'lote' => $request->lote,
            'cep' => $request->cep,
        ]);

        // Rastrear IDs de itens enviados
        $idsEnviados = [];

        // Atualizar ou criar itens do pedido
        foreach ($request->itens as $item) {
            $produto = Produtos::findOrFail($item['produto_id']);
            $precoUnitario = $produto->preco;
            $selectedConfigIds = [];

            if (isset($item['configuracoes']) && is_array($item['configuracoes'])) {
                foreach ($item['configuracoes'] as $opcaoId => $configIds) {
                    $configIds = is_array($configIds) ? $configIds : [$configIds];
                    foreach ($configIds as $configId) {
                        $selectedConfigIds[] = $configId;
                    }
                }
            }

            $configuracoes = ProdutoConfiguracao::whereIn('id', $selectedConfigIds)->get();
            foreach ($configuracoes as $configuracao) {
                $precoUnitario += $configuracao->preco_adicional;
            }

            if (!empty($item['id'])) {
                // Atualizar item existente
                $pedidoItem = PedidoItem::findOrFail($item['id']);
                $pedidoItem->update([
                    'produto_id' => $item['produto_id'],
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $precoUnitario,
                ]);
                $idsEnviados[] = $item['id'];
            } else {
                // Criar novo item
                $novoItem = PedidoItem::create([
                    'pedido_id' => $pedido->id,
                    'produto_id' => $item['produto_id'],
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $precoUnitario,
                ]);
                $idsEnviados[] = $novoItem->id;
                $pedidoItem = $novoItem;
            }

            // Atualizar configurações do item
            PedidoItemConfiguracao::where('pedido_item_id', $pedidoItem->id)->delete();

            if (isset($item['configuracoes']) && is_array($item['configuracoes'])) {
                foreach ($item['configuracoes'] as $opcaoId => $configIds) {
                    $configIds = is_array($configIds) ? $configIds : [$configIds];
                    foreach ($configIds as $configId) {
                        $configuracao = ProdutoConfiguracao::find($configId);
                        if ($configuracao) {
                            PedidoItemConfiguracao::create([
                                'pedido_item_id' => $pedidoItem->id,
                                'produto_opcao_id' => $configuracao->produto_opcao_id,
                                'produto_configuracao_id' => $configId,
                                'quantidade' => 1,
                            ]);
                        }
                    }
                }
            }
        }

        // Deletar itens que foram removidos (não estão no array de enviados)
        PedidoItem::where('pedido_id', $pedido->id)
            ->whereNotIn('id', $idsEnviados)
            ->delete();

        return redirect()->route('pedidos.gerenciar')->with('success', 'Pedido atualizado com sucesso!');
    }

}
