<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\ItemCarrinho;
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

        // Validação condicional para entrega
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

        // Mapeia os itens para formato correto
        $itensPedido = collect($itensCarrinho)->map(function ($item) {
            return [
                'produto' => $item['nome'],
                'quantidade' => $item['quantidade'],
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
        ]);

        // Limpar o carrinho da sessão após finalizar pedido
        session()->forget('carrinho');

        // Montar mensagem para WhatsApp
        $numeroWhatsApp = '5562993847722';
        $mensagem = "Novo Pedido 🚀\n"
            . "Cliente: {$pedido->nome_cliente}\n"
            . "Data de Entrega: {$pedido->data_entrega}\n"
            . "Tipo de Entrega: " . ucfirst($pedido->tipo_entrega) . "\n"
            . "Itens do Pedido:\n";

        foreach ($itensPedido as $item) {
            $mensagem .= "- {$item['produto']} (x{$item['quantidade']})\n";
        }

        // Adicionar endereço se for entrega
        if ($pedido->tipo_entrega === 'entrega') {
            $mensagem .= "\n📍 Endereço de Entrega:\n";
            $mensagem .= "{$pedido->rua}, Nº {$pedido->numero}, Bairro {$pedido->bairro}\n";
            if ($pedido->quadra) $mensagem .= "Quadra: {$pedido->quadra} ";
            if ($pedido->lote) $mensagem .= "Lote: {$pedido->lote} ";
            if ($pedido->cep) $mensagem .= "\nCEP: {$pedido->cep}";
        }

        $linkWhatsApp = "https://wa.me/{$numeroWhatsApp}?text=" . urlencode($mensagem);

        return redirect()->away($linkWhatsApp);
    }



    public function cancelar($id)
    {
        $pedido = Pedido::findOrFail($id);
        $pedido->status = 'Cancelado'; // Define o status como Cancelado
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
        // Busca o pedido pelo ID
        $pedido = Pedido::findOrFail($id);

        // Retorna a view de edição com os dados do pedido
        return view('pedidos.edit', compact('pedido'));
    }

    // Método para visualizar histórico de pedidos
    public function historico()
    {
        // Buscar pedidos com status 'pendente' ou 'em andamento'
        $pedidos = Pedido::whereNotIn('status', ['pendente', 'em andamento'])->get();
        
        // Retornar a view com a lista de pedidos
        return view('pedidos.historico', compact('pedidos'));
    }
}
