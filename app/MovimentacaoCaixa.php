<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MovimentacaoCaixa extends Model {

    protected $fillable = [
        'pedido_id',
        'user_id',
        'parcela_id',
        'valor',
        'valor_desconto',
        'descricao',
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

    public function setValorDescontoAttribute($value) {
        if (substr_count($value, ',') == 0) {
            return $this->attributes['valor_desconto'] = $value;
        } else {
            return $this->attributes['valor_desconto'] = formatValueForMysql($value);
        }
    }

    public function getCreatedAtAttribute($value) {
        try {
            return (new Carbon($value))->format('d/m/Y h:i:s');
        } catch (\Exception $e) {
            return date('d/m/Y');
        }
    }

    public function parcela() {
        return $this->belongsTo(Parcela::class , 'parcela_id');
    }

    public function pedido() {
        return $this->belongsTo(Pedido::class , 'pedido_id');
    }

    public function user() {
        return $this->belongsTo(User::class , 'user_id');
    }

    public function getMovimentacoes($dias = 0) {
        $data = Carbon::now()->subDays($dias)->format('Y-m-d');

        $query = $this->newQuery();

        if (!is_null($dias)) {
            $query->where('created_at', '>=', $data)
                ->where('created_at', '<', Carbon::now()->addDays(1)->format('Y-m-d'));
        }

        return $query->get();
    }
}
