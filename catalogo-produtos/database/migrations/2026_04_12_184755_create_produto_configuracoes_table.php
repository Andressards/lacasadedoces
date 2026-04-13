<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdutoConfiguracoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produto_configuracoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_opcao_id')->constrained('produto_opcoes')->onDelete('cascade');
            $table->string('valor'); // Ex: 'Chocolate', 'Morango', 'Brigadeiro'
            $table->string('descricao')->nullable(); // Descrição opcional
            $table->decimal('preco_adicional', 10, 2)->default(0); // Preço adicional se houver
            $table->integer('quantidade_disponivel')->nullable(); // Para controle de estoque
            $table->boolean('ativo')->default(true);
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
        Schema::dropIfExists('produto_configuracoes');
    }
}
