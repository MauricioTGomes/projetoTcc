<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pedido extends Model {

    protected $fillable = [
        'pessoa_id',
        'user_abertura_id',
        'user_fechamento_id',
        'valor_total',
        'valor_desconto',
        'valor_liquido',
        'observacoes',
        'status',
        'faturado',
        'numero',
        'data_entrega',
        'data_faturamento',
        'data_entrega_realizada',
        'estornado'
    ];

    protected $dates = ['updated_at', 'created_at', 'data_entrega', 'data_faturamento'];

    protected $table = 'pedido';

    public function setValorTotalAttribute($value) {
        if (substr_count($value, ',') == 0) {
            return $this->attributes['valor_total'] = $value;
        } else {
            return $this->attributes['valor_total'] = formatValueForMysql($value);
        }
    }

    public function setValorDescontoAttribute($value) {
        if (substr_count($value, ',') == 0) {
            return $this->attributes['valor_desconto'] = $value;
        } else {
            return $this->attributes['valor_desconto'] = formatValueForMysql($value);
        }
    }

    public function setValorLiquidoAttribute($value) {
        if (substr_count($value, ',') == 0) {
            return $this->attributes['valor_liquido'] = $value;
        } else {
            return $this->attributes['valor_liquido'] = formatValueForMysql($value);
        }
    }

    public function setFaturadoAttribute($value) {
        return $this->attributes['faturado'] = $value ? '1' : '0';
    }

    public function getValorTotalAttribute($value) {
        return formatValueForUser($value);
    }

    public function getValorDescontoAttribute($value) {
        return formatValueForUser($value);
    }

    public function getValorLiquidoAttribute($value) {
        return formatValueForUser($value);
    }

    public function getDataEntregaAttribute($value) {
        return (new Carbon($value))->format('d/m/Y');
    }

    public function getCreatedAtAttribute($value) {
        return (new Carbon($value))->format('d/m/Y');
    }

    public function getDataFaturamentoAttribute($value) {
        return (new Carbon($value))->format('d/m/Y');
    }

    public function getDataEntregaRealizadaAttribute($value) {
        return (new Carbon($value))->format('d/m/Y');
    }

    public function conta() {
        return $this->belongsTo(Conta::class , 'id', 'pedido_id');
    }

    public function pessoa() {
        return $this->hasOne(Pessoa::class , 'id', 'pessoa_id');
    }

    public function movimentacaoCaixa() {
        return $this->belongsTo(MovimentacaoCaixa::class , 'id','pedido_id')->where('movimentacao', 'ENTRADA');
    }

    public function itens() {
        return $this->hasMany(Item::class , 'pedido_id');
    }

    public function getPedidosListagem($estornado = false) {
        return $this->newQuery()
            ->join('pessoa', 'pessoa.id', '=', 'pessoa_id')
            ->where('estornado', $estornado ? '1' : '0')
            ->select(DB::raw("pedido.*, if(pessoa.tipo = 'JURIDICO', CONCAT(pessoa.razao_social, ' (', pessoa.cnpj, ')'), CONCAT(pessoa.nome, ' (', pessoa.cpf, ')')) as nome_pessoa"))->get();
    }
}
