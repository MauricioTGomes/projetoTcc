<?php


namespace App\Http\Controllers;

use App\Conta;
use App\Item;
use App\MovimentacaoCaixa;
use App\Produto;
use Carbon\Carbon;
use Webpatser\Uuid\Uuid;

class ControlaPedido {
    private $moviCaixaModel;
    private $pedido;
    private $contaModel;
    private $produtoModel;
    private $itemModel;

    public function __construct(MovimentacaoCaixa $movimentacaoCaixa, Conta $conta, Produto $produto, Item $item) {
        $this->moviCaixaModel = $movimentacaoCaixa;
        $this->contaModel = $conta;
        $this->produtoModel = $produto;
        $this->itemModel = $item;
    }

    private function baixaEstoque($itens) {
        foreach ($itens as $item) {
            $this->itemModel->insereItem($item, $this->pedido->id);
            $produto = $this->produtoModel->find($item['produto_id']);
            $produto->qtd_estoque = $produto->qtd_estoque - formatValueForMysql($item['quantidade']);
            $this->produtoModel->updateProduto($produto);
        }
    }

    public function retornaEstoque($itens) {
        foreach ($itens as $item) {
            $produto = $this->produtoModel->find($item->produto_id);
            $produto->qtd_estoque = $produto->qtd_estoque + formatValueForMysql($item->quantidade);
            $this->produtoModel->updateProduto($produto);
            $this->itemModel->deleteItem($item->id);
        }
    }

    private function controlaFormaPagPedido() {
        $descricaoNome = is_null($this->pedido->pessoa) ? ' cliente não informado.' : " para cliente " . $this->pedido->pessoa->nome_pessoa;

        $this->moviCaixaModel->insereMovi([
            'pedido_id' => $this->pedido->id,
            'user_id' => auth()->user()->id,
            'valor' => $this->pedido->valor_total,
            'valor_desconto' => $this->pedido->valor_desconto,
            'movimentacao' => 'ENTRADA',
            'descricao' => "Lançamento pedido de número: " . $this->pedido->numero . $descricaoNome,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    public function controlaPedido($pedido, $input, $update = false) {
        $this->pedido = $pedido;

        if ($update) {
            $this->retornaEstoque($pedido->itens);
        }

        $this->baixaEstoque($input['itens']);

        if ($input['faturado']) {
            $this->controlaFormaPagPedido();
        }
    }
}
