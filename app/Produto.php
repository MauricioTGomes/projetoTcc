<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Produto extends Model
{

    protected $table = 'produto';

    protected $fillable = [
        'nome',
        'apelido_produto',
        'qtd_estoque',
        'valor_venda',
        'codigo',
        'ativo'
    ];

    public function deleteProduto($idProduto) {
        return DB::delete('delete from produto where id = ?', [$idProduto]);
    }

    public function insertProduto($array) {
        return DB::insert("insert into produto (nome, apelido_produto, qtd_estoque, valor_venda, codigo, ativo) values (?, ?, ?, ?, ?, ?)", [$array['nome'], $array['apelido_produto'], formatValueForMysql($array['qtd_estoque']), formatValueForMysql($array['valor_venda']), $array['codigo'], $array['ativo']]);
    }

    public function updateProduto($produto) {
        return DB::update("update produto set nome = ?, apelido_produto = ?, qtd_estoque = ?, valor_venda = ?, codigo = ?, ativo = ? where id = ?", [$produto->nome, $produto->apelido_produto, $produto->qtd_estoque, $produto->valor_venda, $produto->codigo, $produto->ativo, $produto->id]);
    }

    public function find($id) {
        return DB::select("select * from produto where id = ?", [$id])[0];
    }

    public function listagem($inativo = false) {
        return DB::select("select * from produto where ativo = ?", [$inativo ? '0' : '1']);
    }
}
