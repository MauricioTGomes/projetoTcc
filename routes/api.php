<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/getCidade', 'MenuController@getCidade');

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

    Route::post('/movimentacaoCaixa/getAll', 'MovimentacaoController@listar');
    Route::group(['where' => ['id' => '[0-9]+'], 'prefix' => 'contas'], function () {
        Route::post('/listar', 'ContaController@getContas');
        Route::post('/get/{id}', 'ContaController@getConta');
        Route::post('/gravar', 'ContaController@gravar');
        Route::post('/update', 'ContaController@update');
        Route::post('/deletar/{id}', 'ContaController@excluir');
        Route::post('/calculaParcelas', 'ContaController@calculaParcela');
        Route::post('/baixarParcela/{id}', 'ParcelaController@baixarParcela');
        Route::post('/estornarParcela/{id}', 'ParcelaController@estornoParcela');
    });

    Route::group(['where' => ['id' => '[0-9]+'], 'prefix' => 'pedidos'], function () {
        Route::post('/listar', 'PedidoController@getPedidos');
        Route::post('/get/{id}', 'PedidoController@getPedido');
        Route::post('/gravar', 'PedidoController@gravar');
        Route::post('/update', 'PedidoController@update');
        Route::post('/deletar/{id}', 'PedidoController@excluir');
        Route::post('/alteraStatus/{id}', 'PedidoController@alteraStatus');
    });

    Route::group(['where' => ['id' => '[0-9]+'], 'prefix' => 'usuarios'], function () {
        Route::post('/listar', 'UserController@getUsuarios');
        Route::post('/get/{id}', 'UserController@getUsuario');
        Route::post('/gravar', 'UserController@gravar');
        Route::post('/update', 'UserController@update');
        Route::post('/deletar/{id}', 'UserController@excluir');
    });
});

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});
