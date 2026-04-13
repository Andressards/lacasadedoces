<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdutoOpcoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produto_opcoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_id')->constrained('produtos')->onDelete('cascade');
            $table->string('categoria'); // Ex: 'bolo', 'doces', 'salgados'
            $table->string('nome'); // Ex: 'Sabor da Massa', 'Sabor do Recheio', 'Sabores dos Doces'
            $table->string('tipo'); // 'selecao_unica', 'selecao_multipla', 'quantidade_fixa'
            $table->integer('quantidade_minima')->nullable(); // Para seleções múltiplas
            $table->integer('quantidade_maxima')->nullable(); // Para seleções múltiplas
            $table->boolean('obrigatorio')->default(true);
            $table->integer('ordem')->default(0); // Para ordenação
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
        Schema::dropIfExists('produto_opcoes');
    }
}
