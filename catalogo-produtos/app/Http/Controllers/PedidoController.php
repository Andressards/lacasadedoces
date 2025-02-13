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
            'data_entrega' => 'required|date|after:today',
        ]);

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

        // Salvar pedido (ajuste conforme necessidade)
        $pedido = Pedido::create([
            'nome_cliente' => $request->nome_cliente,
            'data_entrega' => $request->data_entrega,
            'itens_pedido' => $itensPedido,
        ]);

        // Limpar o carrinho da sessão após finalizar pedido
        session()->forget('carrinho');

        // Gerar mensagem para WhatsApp
        $numeroWhatsApp = '5562993847722';
        $mensagem = "Novo Pedido 🚀\n"
            . "Cliente: {$pedido->nome_cliente}\n"
            . "Data de Entrega: {$pedido->data_entrega}\n"
            . "Itens do Pedido:\n";

        foreach ($itensPedido as $item) {
            $mensagem .= "- {$item['produto']} (x{$item['quantidade']})\n";
        }

        $linkWhatsApp = "https://wa.me/{$numeroWhatsApp}?text=" . urlencode($mensagem);

        return redirect()->away($linkWhatsApp);
    }
}
