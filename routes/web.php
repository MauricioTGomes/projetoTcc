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

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/getCidadesSelect', 'MenuController@getCidadesSelect');
    
    Route::group(['where' => ['id' => '[0-9]+'], 'prefix' => 'pessoas'], function () {
        Route::post('/listar', 'PessoaController@getPessoas');
        Route::post('/get/{idPessoa}', 'PessoaController@getPessoa');
        Route::post('/gravar', 'PessoaController@gravar');
        Route::post('/update', 'PessoaController@update');
        Route::post('/deletar/{id}', 'PessoaController@excluir');
    });

    Route::group(['where' => ['id' => '[0-9]+'], 'prefix' => 'produtos'], function () {
        Route::post('/listar', 'ProdutoController@getProdutos');
        Route::post('/get/{idPessoa}', 'ProdutoController@getProduto');
        Route::post('/gravar', 'ProdutoController@gravar');
        Route::post('/update', 'ProdutoController@update');
        Route::post('/deletar/{id}', 'ProdutoController@excluir');
    });
});

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});

Route::get('/{any}', function () {
    return view('layouts.app');
})->where('any', '.*');


