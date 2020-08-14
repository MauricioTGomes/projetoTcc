<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

    public function setQtdEstoqueAttribute($value) {
        return $this->attributes['qtd_estoque'] = formatValueForMysql($value);
    }

    public function setValorVendaAttribute($value) {
        return $this->attributes['valor_venda'] = formatValueForMysql($value);
    }

    public function getQtdEstoqueAttribute($value) {
        return formatValueForUser($value);
    }

    public function getValorVendaAttribute($value) {
        return formatValueForUser($value);
    }
}