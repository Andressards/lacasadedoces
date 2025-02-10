<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAtivoToProdutosTable extends Migration
{
    public function up()
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->boolean('ativo')->default(true); // Define 'true' como valor padrão (ativo)
        });
    }

    public function down()
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropColumn('ativo');
        });
    }
}
