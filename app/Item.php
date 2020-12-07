<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Item extends Model {
    protected $table = 'item';

    protected $fillable = array(
        'produto_id',
        'pedido_id',
        'valor_total',
        'quantidade',
        'valor_unitario',
    );

    public function insereItem($array, $pedidoId) {
        DB::insert("insert into item (produto_id, pedido_id, valor_total, quantidade, valor_unitario) values (?, ?, ?, ?, ?)", [$array['produto_id'], $pedidoId, formatValueForMysql($array['valor_total']), formatValueForMysql($array['quantidade']), formatValueForMysql($array['valor_unitario'])]);
    }

    public function deleteItem($itemId) {
        return DB::delete('delete from item where id = ?', [$itemId]);
    }
}
