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

            $pedido = $this->pedidoModel->create($input + [
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

    public function imprimePedido($id) {
        $parametros['pedido'] = $this->pedidoModel->find($id);
        $snappy               = App::make('snappy.pdf.wrapper');
        $snappy->setOption('header-html', view('layouts.header_relatorios')->render());
        $snappy->setOption('footer-html', view('layouts.footer_relatorios')->render());
        $snappy->loadView('pedido.conteudo', $parametros);

        return \PDF::loadView('site.certificate.certificate', $parametros)
        ->download('nome-arquivo-pdf-gerado.pdf');

        return $snappy->download('Pedido - '.$parametros['pedido']->numero);
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

    public function update(Request $request) {
        try {
            DB::beginTransaction();
            $input = $request->all();
            $pedidoAnterior = $this->pedidoModel->find($input['id']);
            $this->controlaPedido->controlaPedido($pedidoAnterior, $input, true);
            $input['data_faturamento'] = $input['faturado'] ? Carbon::now() : null;
            $input['data_entrega_realizada'] = null;
            $pedidoAnterior->update($input);
            DB::commit();
            return response()->json(['erro' => 0, 'mensagem' => 'Sucesso ao alterar pedido.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['erro' => 1, 'mensagem' => $e->getFile() . $e->getLine()]);
        }

    }

    public function getPedidos(Request $request) {
        $pedidos = $this->pedidoModel->getPedidosListagem($request->input('estornado'));
        return response()->json($pedidos);
    }

    public function getPedido($id) {
        $pedido = $this->pedidoModel->find($id)->load('itens.produto', 'pessoa');
        return response()->json($pedido);
    }

    public function alteraStatus($id) {
        $pedido = $this->pedidoModel->find($id);

        $status = '0';
        $data = null;
        if ($pedido->status == 0) {
            $status = '1';
            $data = Carbon::now();
        }

        $pedido->status = $status;
        $pedido->data_entrega_realizada = $data;
        $pedido->update();
        return response(204);
    }
}
