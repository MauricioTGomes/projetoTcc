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

    public function movimentacaoCaixa() {
        return $this->belongsTo(MovimentacaoCaixa::class , 'id','pedido_id')->where('movimentacao', 'ENTRADA');
    }

    public function getPedidosListagem($estornado = false) {
        return DB::select("select date_format(pedido.data_entrega, '%d/%m/%Y') as data_entrega_f, date_format(pedido.data_entrega_realizada, '%d/%m/%Y') as data_entrega_realizada_f, date_format(pedido.created_at, '%d/%m/%Y') as created_at_f, pedido.*, if(pessoa.tipo = 'JURIDICO', CONCAT(pessoa.razao_social, ' (', pessoa.cnpj, ')'), CONCAT(pessoa.nome, ' (', pessoa.cpf, ')')) as nome_pessoa from pedido left join pessoa on pessoa.id = pedido.pessoa_id where estornado = ?", [$estornado ? '1' : '0']);
    }

    public function insertPedido($array) {
        return DB::update("insert into pedido (pessoa_id, user_abertura_id, user_fechamento_id, valor_total, valor_desconto, valor_liquido, observacoes, status, faturado, numero, data_entrega, data_faturamento, data_entrega_realizada, estornado) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [$array['pessoa_id'], $array['user_abertura_id'], $array['user_fechamento_id'], $array['valor_total'], $array['valor_desconto'], $array['valor_liquido'], $array['observacoes'], $array['status'], $array['faturado'], $array['numero'], $array['data_entrega'], $array['data_faturamento'], $array['data_entrega_realizada'], $array['estornado']]);
    }

    public function updateStatus($id, $status, $data) {
        return DB::update("update pedido set status = ?, data_entrega_realizada = ? where id = ?", [$status, $data, $id]);
    }

    public function find($id, $itens = false) {
        $pedido = DB::select("select * from pedido where id = ?", [$id])[0];
        $pedido->pessoa = is_null($pedido->pessoa_id) ? null : DB::select("select * from pessoa where id = ?", [$pedido->pessoa_id]);
        if ($itens) {
            $pedido->itens = DB::select("select * from item where pedido_id = ?", [$id]);
            foreach ($pedido->itens as $index => $item) {
                $pedido->itens[$index]->produto = DB::select("select * from produto where id = ?", [$item->produto_id]);
            }
        }
        return $pedido;
    }
}
