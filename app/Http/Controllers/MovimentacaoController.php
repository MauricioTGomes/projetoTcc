<?php

namespace App\Http\Controllers;

use App\MovimentacaoCaixa;
use Illuminate\Http\Request;

class MovimentacaoController extends Controller {
    public function __construct() {}

    public function listar(Request $request, MovimentacaoCaixa $movimentacaoCaixaModel) {
        $movimentacoes = $movimentacaoCaixaModel->getMovimentacoes($request->input('dias'));
        $parametros = [
            'movimentacoes' => $movimentacoes,
            'totalVendas' => 0,
            'entradas' => 0,
            'saidas' => 0,
            'total' => 0
        ];

        foreach ($movimentacoes as $key => $movi) {
            if ($movi->movimentacao === 'SAIDA') {
                $parametros['saidas'] += ($movi->valor - $movi->valor_desconto);
            }

            if ($movi->movimentacao === 'ENTRADA') {
                $parametros['entradas'] += ($movi->valor - $movi->valor_desconto);
            }

            if (!is_null($movi->pedido) && $movi->pedido->faturado === '1' && $movi->pedido->estornado === '0') {
                $parametros['totalVendas'] += ($movi->valor - $movi->valor_desconto);
            }
        }

        $parametros['total'] = $parametros['entradas'] - $parametros['saidas'];

        return response()->json($parametros);
    }

}
