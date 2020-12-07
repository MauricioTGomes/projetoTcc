<?php

namespace App\Http\Controllers;

use App\MovimentacaoCaixa;
use App\Pedido;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller {

    private $pedidoModel;
    private $controlaPedido;

    public function __construct(Pedido $pedido, ControlaPedido $controlaPedido) {
        $this->pedidoModel = $pedido;
        $this->controlaPedido = $controlaPedido;
    }

    public function gravar(Request $request) {
        try {
            DB::beginTransaction();
            $input = $request->all();

            $pedido = $this->pedidoModel->insertPedido($input + [
                 'user_abertura_id' => auth()->user()->id,
                 'user_fechamento_id' => $input['faturado'] ? auth()->user()->id : null,
                 'data_faturamento' => $input['faturado'] ? Carbon::now() : null,
                 'numero' => $this->pedidoModel->all()->last()->numero + 1
            ]);

            $this->controlaPedido->controlaPedido($pedido, $input);
            DB::commit();
            return response()->json(['erro' => 0, 'mensagem' => "Pedido emitido com sucesso!"]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['erro' => 1, 'mensagem' => $e->getMessage()]);
        }

    }

    public function excluir($id) {
        try {
            DB::beginTransaction();
            $pedido = $this->pedidoModel->find($id);

            if (isset($pedido->conta) && !is_null($pedido->conta->parcelasPagas->first())) {
                throw new \Exception("Pedido com movimentação financeira.");
            } else {
                $this->controlaPedido->retornaEstoque($pedido->itens);

                if (isset($pedido->conta)) {
                    $pedido->conta()->delete();
                }

                if(!is_null($pedido->movimentacaoCaixa)) {
                    MovimentacaoCaixa::create([
                        'pedido_id' => $pedido->id,
                        'user_id' => auth()->user()->id,
                        'valor' => $pedido->valor_total,
                        'valor_desconto' => $pedido->valor_desconto,
                        'movimentacao' => 'SAIDA',
                        'descricao' => "Estorno pedido de número: " . $pedido->numero . (is_null($pedido->pessoa) ? ' cliente não informado.' : " para cliente " . $pedido->pessoa->nome_documento_completo),
                    ]);
                }

                $pedido->update(['estornado' => '1']);
            }
            DB::commit();
            return response()->json(['erro' => 0, 'mensagem' => "Sucesso ao eliminar pedido"]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['erro' => 1, 'mensagem' => "Erro ao eliminar, ".$exception->getMessage()]);
        }
    }

    public function getPedidos(Request $request) {
        $pedidos = $this->pedidoModel->getPedidosListagem($request->input('estornado'));
        return response()->json($pedidos);
    }

    public function getPedido($id) {
        return response()->json($this->pedidoModel->find($id, true));
    }

    public function alteraStatus($id) {
        $pedido = $this->pedidoModel->find($id);

        $status = '0';
        $data = null;
        if ($pedido->status == 0) {
            $status = '1';
            $data = Carbon::now();
        }
        $this->pedidoModel->updateStatus($id, $status, $data);
        return response(204);
    }
}
