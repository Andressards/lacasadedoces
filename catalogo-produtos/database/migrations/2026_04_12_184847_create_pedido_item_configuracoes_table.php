<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidoItemConfiguracoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedido_item_configuracoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_item_id')->constrained('pedido_itens')->onDelete('cascade');
            $table->foreignId('produto_opcao_id')->constrained('produto_opcoes')->onDelete('cascade');
            $table->foreignId('produto_configuracao_id')->constrained('produto_configuracoes')->onDelete('cascade');
            $table->integer('quantidade')->default(1); // Para opções com quantidade
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedido_item_configuracoes');
    }
}
