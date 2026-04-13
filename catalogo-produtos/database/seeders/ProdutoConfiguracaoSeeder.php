<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProdutoConfiguracaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Buscar ou criar categoria
        $categoria = \App\Models\Categoria::firstOrCreate(
            ['nome' => 'Kits e Conjuntos'],
            ['ativo' => true]
        );

        // Criar um produto de exemplo: Kit Festa Pequeno
        $produto = \App\Models\Produtos::create([
            'nome' => 'Kit Festa Pequeno',
            'descricao' => 'Kit completo para festa com bolo, doces e salgados',
            'preco' => 89.90,
            'categoria_id' => $categoria->id,
            'ativo' => true,
        ]);

        // 1. Opções para Bolo
        $opcaoBoloMassa = \App\Models\ProdutoOpcao::create([
            'produto_id' => $produto->id,
            'categoria' => 'bolo',
            'nome' => 'Sabor da Massa',
            'tipo' => 'selecao_unica',
            'obrigatorio' => true,
            'ordem' => 1,
        ]);

        $opcaoBoloRecheio = \App\Models\ProdutoOpcao::create([
            'produto_id' => $produto->id,
            'categoria' => 'bolo',
            'nome' => 'Sabor do Recheio',
            'tipo' => 'selecao_unica',
            'obrigatorio' => true,
            'ordem' => 2,
        ]);

        // 2. Opções para Doces (seleção múltipla com quantidade)
        $opcaoDoces = \App\Models\ProdutoOpcao::create([
            'produto_id' => $produto->id,
            'categoria' => 'doces',
            'nome' => 'Sabores dos Doces (escolha 3 sabores)',
            'tipo' => 'selecao_multipla',
            'quantidade_minima' => 3,
            'quantidade_maxima' => 3,
            'obrigatorio' => true,
            'ordem' => 3,
        ]);

        // 3. Opções para Salgados (seleção múltipla com quantidade)
        $opcaoSalgados = \App\Models\ProdutoOpcao::create([
            'produto_id' => $produto->id,
            'categoria' => 'salgados',
            'nome' => 'Sabores dos Salgados (escolha 2 sabores)',
            'tipo' => 'selecao_multipla',
            'quantidade_minima' => 2,
            'quantidade_maxima' => 2,
            'obrigatorio' => true,
            'ordem' => 4,
        ]);

        // Configurações para Massa do Bolo
        \App\Models\ProdutoConfiguracao::create([
            'produto_opcao_id' => $opcaoBoloMassa->id,
            'valor' => 'Baunilha',
            'ordem' => 1,
        ]);
        \App\Models\ProdutoConfiguracao::create([
            'produto_opcao_id' => $opcaoBoloMassa->id,
            'valor' => 'Chocolate',
            'ordem' => 2,
        ]);
        \App\Models\ProdutoConfiguracao::create([
            'produto_opcao_id' => $opcaoBoloMassa->id,
            'valor' => 'Cenoura',
            'ordem' => 3,
        ]);

        // Configurações para Recheio do Bolo
        \App\Models\ProdutoConfiguracao::create([
            'produto_opcao_id' => $opcaoBoloRecheio->id,
            'valor' => 'Brigadeiro',
            'ordem' => 1,
        ]);
        \App\Models\ProdutoConfiguracao::create([
            'produto_opcao_id' => $opcaoBoloRecheio->id,
            'valor' => 'Doce de Leite',
            'ordem' => 2,
        ]);
        \App\Models\ProdutoConfiguracao::create([
            'produto_opcao_id' => $opcaoBoloRecheio->id,
            'valor' => 'Morango',
            'ordem' => 3,
        ]);

        // Configurações para Doces
        $doces = ['Brigadeiro', 'Beijinho', 'Leite Ninho'];
        foreach ($doces as $index => $doce) {
            \App\Models\ProdutoConfiguracao::create([
                'produto_opcao_id' => $opcaoDoces->id,
                'valor' => $doce,
                'ordem' => $index + 1,
            ]);
        }

        // Configurações para Salgados
        $salgados = ['Coxinha', 'Risole', 'Empada', 'Enroladinho', 'Pastel'];
        foreach ($salgados as $index => $salgado) {
            \App\Models\ProdutoConfiguracao::create([
                'produto_opcao_id' => $opcaoSalgados->id,
                'valor' => $salgado,
                'ordem' => $index + 1,
            ]);
        }
    }
}
