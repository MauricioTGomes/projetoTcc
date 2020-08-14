<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovimentacaoCaixa extends Model {

    protected $fillable = [
        'pedido_id',
        'user_id',
        'parcela_id',
        'valor',
        'descricao',
        'operacao',
        'movimentacao'
    ];

    protected $table = 'movimentacao_caixa';

    protected $dates = ['updated_at', 'created_at'];

    public function setValorAttribute($value) {
        if (substr_count($value, ',') == 0) {
            return $this->attributes['valor'] = $value;
        } else {
            return $this->attributes['valor'] = formatValueForMysql($value);
        }
    }

    public function parcela() {
        return $this->belongsTo(Parcela::class , 'parcela_id');
    }

    public function pedido() {
        //return $this->belongsTo(Pedido::class , 'pedido_id');
    }

    public function user() {
        return $this->belongsTo(User::class , 'user_id');
    }
}
