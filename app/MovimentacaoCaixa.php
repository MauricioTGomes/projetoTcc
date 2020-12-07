<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MovimentacaoCaixa extends Model {

    protected $fillable = [
        'pedido_id',
        'user_id',
        'parcela_id',
        'valor',
        'valor_desconto',
        'descricao',
        'movimentacao',
    ];

    protected $table = 'movimentacao_caixa';

    protected $dates = ['updated_at', 'created_at'];

    public function insereMovi($array) {
        DB::insert("insert into item (pedido_id, user_id, parcela_id, valor, valor_desconto, descricao, movimentacao) values (?, ?, null, ?, ?, ?, ?)", [$array['pedido_id'], $array['user_id'], $array['valor'], $array['valor_desconto'], $array['descricao'], $array['movimentacao']]);
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
