<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddObsEnderecoToPedidos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->text('observacao')->nullable();
            $table->string('rua')->nullable();
            $table->string('quadra')->nullable();
            $table->string('lote')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cep')->nullable();
            $table->string('numero')->nullable();
            $table->string('numero_contato')->nullable();
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pedidos', function (Blueprint $table) {
            //
        });
    }
}
