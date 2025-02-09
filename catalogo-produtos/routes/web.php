<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\ProdutosController;

Route::get('/', [ProdutosController::class, 'create'])->name('produtos.create');
Route::get('/produtos/create', [ProdutosController::class, 'create'])->name('produtos.create');
Route::post('/produtos/store', [ProdutosController::class, 'storeProdutos'])->name('produtos.store');

// routes/web.php
use App\Http\Controllers\CatalogoController;

Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo.index');  // Página do catálogo com todos os produtos
Route::get('/catalogo/{id}', [CatalogoController::class, 'show'])->name('catalogo.show');  // Página de detalhes de um produto
Route::post('/catalogo/pedido', [CatalogoController::class, 'storePedido'])->name('catalogo.pedido');  // Formulário de pedido
Route::post('/catalogo/adicionar-carrinho', [CatalogoController::class, 'adicionarCarrinho'])->name('catalogo.adicionarCarrinho');
Route::get('/carrinho', [CatalogoController::class, 'exibirCarrinho'])->name('catalogo.carrinho');

