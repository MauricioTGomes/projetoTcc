<?php


namespace App\Http\Controllers;

use App\Conta;
use App\MovimentacaoCaixa;
use App\Produto;
use Webpatser\Uuid\Uuid;

class ControlaPedido {
    private $moviCaixaModel;
    private $pedido;
    private $contaModel;
    private $produtoModel;

    public function __construct(MovimentacaoCaixa $movimentacaoCaixa, Conta $conta, Produto $produto) {
        $this->moviCaixaModel = $movimentacaoCaixa;
        $this->contaModel = $conta;
        $this->produtoModel = $produto;
    }

    private function baixaEstoque($itens) {
        foreach ($itens as $item) {
            $this->pedido->itens()->create($item);
            $produto = $this->produtoModel->find($item['produto_id']);
            $produto->qtd_estoque = formatValueForMysql($produto->qtd_estoque) - formatValueForMysql($item['quantidade']);
            $produto->update($produto->toArray());
        }
    }

    public function retornaEstoque($itens) {
        foreach ($itens as $item) {
            $produto = $this->produtoModel->find($item->produto_id);
            $produto->qtd_estoque = formatValueForMysql($produto->qtd_estoque) + formatValueForMysql($item->quantidade);
            $produto->update($produto->toArray());
            $item->delete();
        }
    }

    private function controlaFormaPagPedido($formaPagamento) {
        if ($formaPagamento['tipo'] == 'VISTA') {
            $descricaoNome = is_null($this->pedido->pessoa) ? ' cliente não informado.' : " para cliente " . $this->pedido->pessoa->nome_documento_completo;

            $this->moviCaixaModel->create([
                'pedido_id' => $this->pedido->id,
                'user_id' => auth()->user()->id,
                'valor' => $this->pedido->valor_total,
                'valor_desconto' => $this->pedido->valor_desconto,
                'movimentacao' => 'ENTRADA',
                'descricao' => "Lançamento pedido de número: " . $this->pedido->numero . $descricaoNome,
            ]);
        } else {
            if (is_null($this->pedido->pessoa)) throw new \Exception("Informe uma pessoa antes de gravar");

            $conta = $this->contaModel->create([
                'pessoa_id' => $this->pedido->pessoa_id,
                'pedido_id' => $this->pedido->id,
                'titulo' => strtoupper(Uuid::generate()),
                'data_emissao' => date('d/m/Y'),
                'user_id' => auth()->user()->id,
                'vlr_total' => $this->pedido->valor_total,
                'vlr_restante' => $this->pedido->valor_total,
                'tipo_operacao' => 'R',
                'qtd_dias' => $formaPagamento['qtd_dias']
            ]);
            foreach ($formaPagamento['array_parcelas'] as $index => $parcela) {
                $conta->parcelas()->create(['valor_original' => $parcela['valor']] + $parcela);
            }
        }
    }

    public function controlaPedido($pedido, $input, $update = false) {
        $this->pedido = $pedido;

        if ($update) {
            $this->retornaEstoque($pedido->itens);
        }

        $this->baixaEstoque($input['itens']);

        if ($input['faturado']) {
            $this->controlaFormaPagPedido($input['formaPagamento']);
        }
    }
}
