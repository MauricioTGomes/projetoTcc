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

    public function getPedidosListagem($estornado = false) {
        return DB::select("select date_format(pedido.data_entrega, '%d/%m/%Y') as data_entrega_f, date_format(pedido.data_entrega_realizada, '%d/%m/%Y') as data_entrega_realizada_f, date_format(pedido.created_at, '%d/%m/%Y') as created_at_f, pedido.*, if(pessoa.tipo = 'JURIDICO', CONCAT(pessoa.razao_social, ' (', pessoa.cnpj, ')'), CONCAT(pessoa.nome, ' (', pessoa.cpf, ')')) as nome_pessoa from pedido left join pessoa on pessoa.id = pedido.pessoa_id where estornado = ?", [$estornado ? '1' : '0']);
    }

    public static function ultimoPedido()
    {
        $todos = DB::select("select * from pedido");
        $count = count($todos);
        return $count > 0 ? $todos[$count - 1] : null;
    }

    public function insertPedido($array) {
        return DB::insert("insert into pedido (pessoa_id, user_abertura_id, valor_total, valor_desconto, valor_liquido, status, numero, data_entrega) values (?, ?, ?, ?, ?, ?, ?, ?)", [$array['pessoa_id'], $array['user_abertura_id'], formatValueForMysql($array['valor_total']), formatValueForMysql($array['valor_desconto']), formatValueForMysql($array['valor_liquido']), $array['status'], $array['numero'], $array['data_entrega']]);
    }

    public function updateStatus($id, $status, $data) {
        return DB::update("update pedido set status = ?, data_entrega_realizada = ? where id = ?", [$status, $data, $id]);
    }

    public function find($id, $itens = false) {
        $pedido = DB::select("select * from pedido where id = ?", [$id])[0];
        $pedido->pessoa = is_null($pedido->pessoa_id) ? null : DB::select("select *, if(pessoa.tipo = 'JURIDICO', CONCAT(pessoa.razao_social, ' (', pessoa.cnpj, ')'), CONCAT(pessoa.nome, ' (', pessoa.cpf, ')')) as nome_pessoa from pessoa where id = ?", [$pedido->pessoa_id])[0];
        if ($itens) {
            $pedido->itens = DB::select("select * from item where pedido_id = ?", [$id]);
            foreach ($pedido->itens as $index => $item) {
                $pedido->itens[$index]->produto = DB::select("select * from produto where id = ?", [$item->produto_id]);
            }
        }
        return $pedido;
    }
}
