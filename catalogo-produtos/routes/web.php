<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\ProdutosController;

Route::get('/', [ProdutosController::class, 'create'])->name('produtos.create');
Route::get('/produtos/create', [ProdutosController::class, 'create'])->name('produtos.produtos_create');
Route::post('/produtos/store', [ProdutosController::class, 'storeProdutos'])->name('produtos.store');
Route::get('/produtos/{id}/edit', [ProdutosController::class, 'edit'])->name('produtos.edit');
Route::put('/produtos/{id}', [ProdutosController::class, 'update'])->name('produtos.update');
Route::get('/produtos', [ProdutosController::class, 'index'])->name('produtos.index');
Route::get('/produtos/{id}/toggle-status', [ProdutosController::class, 'toggleStatus'])->name('produtos.toggleStatus');

use App\Http\Controllers\CatalogoController;

Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo.index');  // Página do catálogo com todos os produtos
Route::get('/catalogo/{id}', [CatalogoController::class, 'show'])->name('catalogo.show');  // Página de detalhes de um produto
Route::post('/catalogo/pedido', [CatalogoController::class, 'storePedido'])->name('catalogo.pedido');  // Formulário de pedido
Route::post('/catalogo/adicionar-carrinho', [CatalogoController::class, 'adicionarCarrinho'])->name('catalogo.adicionarCarrinho');
Route::get('/carrinho', [CatalogoController::class, 'exibirCarrinho'])->name('catalogo.carrinho');
Route::post('/carrinho/atualizar/{id}', [CatalogoController::class, 'atualizarCarrinho'])->name('carrinho.atualizar');  // Atualizar quantidade do item no carrinho
Route::delete('/carrinho/remover/{id}', [CatalogoController::class, 'removerDoCarrinho'])->name('carrinho.remover');  // Remover item do carrinho

use App\Http\Controllers\CategoriasController;

Route::get('/categorias/create', [CategoriasController::class, 'create'])->name('categorias.create');
Route::post('/categorias', [CategoriasController::class, 'store'])->name('categorias.store');
Route::get('/categorias/{id}/edit', [CategoriasController::class, 'edit'])->name('categorias.edit');
Route::put('/categorias/{id}', [CategoriasController::class, 'update'])->name('categorias.update');
Route::get('/categorias', [CategoriasController::class, 'index'])->name('categorias.index');
Route::get('/categorias/{id}/toggleStatus', [CategoriasController::class, 'toggleStatus'])->name('categorias.toggleStatus');




