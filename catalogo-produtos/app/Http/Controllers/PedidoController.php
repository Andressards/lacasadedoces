<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\ItemCarrinho;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PedidoController extends Controller
{
    public function formularioPedido()
    {
        $itensCarrinho = ItemCarrinho::where('usuario_id', Auth::id())->with('produto')->get();
        return view('pedidos.finalizar', compact('itensCarrinho'));
    }

    public function salvarPedido(Request $request)
{
    $request->validate([
        'nome_cliente' => 'required|string|max:255',
        'data_entrega' => 'required|date|after:today',
    ]);

    $usuarioId = Auth::id();
    $itensCarrinho = ItemCarrinho::where('usuario_id', $usuarioId)->with('produto')->get();

    if ($itensCarrinho->isEmpty()) {
        return redirect()->route('catalogo.carrinho')->with('error', 'Seu carrinho está vazio.');
    }

    // Mapeia os itens do carrinho para um formato correto
    $itensPedido = $itensCarrinho->map(function ($item) {
        return [
            'produto' => $item->produto->nome,
            'quantidade' => $item->quantidade,
        ];
    })->toArray(); // Convertendo para array

    // Salva no banco
    $pedido = Pedido::create([
        'usuario_id' => $usuarioId,
        'nome_cliente' => $request->nome_cliente,
        'data_entrega' => $request->data_entrega,
        'itens_pedido' => $itensPedido, // Laravel automaticamente converte para JSON
    ]);

    // Limpar o carrinho após finalizar pedido
    ItemCarrinho::where('usuario_id', $usuarioId)->delete();

    // Gerar link do WhatsApp
    $numeroWhatsApp = '5562993847722';
    $mensagem = "Novo Pedido 🚀\n"
        . "Cliente: {$pedido->nome_cliente}\n"
        . "Data de Entrega: {$pedido->data_entrega}\n"
        . "Itens do Pedido:\n";

    foreach ($itensCarrinho as $item) {
        $mensagem .= "- {$item->produto->nome} (x{$item->quantidade})\n";
    }

    $linkWhatsApp = "https://wa.me/{$numeroWhatsApp}?text=" . urlencode($mensagem);

    // Redirecionar diretamente para o WhatsApp
    return redirect()->away($linkWhatsApp);
}



    private function enviarWhatsApp($pedido)
    {
        $numeroWhatsApp = '5562993847722'; // Substitua pelo número correto
        $mensagem = "Novo Pedido 🚀\n"
            . "Cliente: {$pedido->nome_cliente}\n"
            . "Data de Entrega: {$pedido->data_entrega}\n"
            . "Itens do Pedido:\n";

        foreach ($pedido->itens_pedido as $item) {
            $mensagem .= "- {$item['produto']} (x{$item['quantidade']})\n";
        }

        $linkWhatsApp = "https://wa.me/{$numeroWhatsApp}?text=" . urlencode($mensagem);

        return redirect()->away($linkWhatsApp);
    }
}