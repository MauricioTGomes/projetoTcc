<?php

namespace App\Http\Controllers;

use App\Conta;
use App\MovimentacaoCaixa;
use App\Parcela;

use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParcelaController extends Controller {

	private $parcelaModel;
	private $contaModel;
	private $movimentacaoCaixa;

	public function __construct(Parcela $parcelaModel, Conta $contaModel, MovimentacaoCaixa $movimentacaoCaixa) {
		$this->parcelaModel      = $parcelaModel;
		$this->contaModel        = $contaModel;
		$this->movimentacaoCaixa = $movimentacaoCaixa;
	}

	public function baixarParcela($id) {
		try {
			DB::beginTransaction();
			$parcela = $this->parcelaModel->find($id);
			$parcela->valor = 0;
			$parcela->data_pagamento = Carbon::now()->format('Y-m-d');
			$parcela->baixada = 1;
			$parcela->save();

			$parcela->conta->vlr_restante = formatValueForUser(formatValueForMysql($parcela->conta->vlr_restante) - $parcela->valor_original);
			$parcela->conta->save();

			$descricao = "Recebimento parcela: ".$parcela->nro_parcela."/".count($parcela->conta->parcelas)." do título ".$parcela->conta->titulo;
			if ($parcela->conta->tipo_operacao == 'P') {
				$descricao = "Pagamento parcela: ".$parcela->nro_parcela."/".count($parcela->conta->parcelas)." do título ".$parcela->conta->titulo;
			}

			$this->movimentacaoCaixa->create([
			    'user_id' => auth()->user()->id,
				'valor' => $parcela->valor_original,
				'parcela_id' => $parcela->id,
				'descricao' => $descricao . "\r\nPessoa: " . $parcela->conta->pessoa->nome_documento_completo,
				'movimentacao' => $parcela->conta->tipo_operacao == 'R' ? 'ENTRADA' : 'SAIDA'
            ]);

			DB::commit();
			return response()->json(['erro' => false, 'mensagem' => 'Baixa realizada com sucesso!']);
		} catch (\Exception $e) {
			DB::rollback();
			return response()->json(['erro' => true, 'mensagem' => $e->getMessage()]);
		}
	}

	public function estornoParcela($id) {
		try {
			DB::beginTransaction();
			$parcela = $this->parcelaModel->find($id);
			$parcela->valor = formatValueForMysql($parcela->valor_original);
			$parcela->baixada = 0;
			$parcela->save();

			$parcela->conta->vlr_restante = formatValueForUser(formatValueForMysql($parcela->conta->vlr_restante)+$parcela->valor);
			$parcela->conta->save();
			$this->movimentacaoCaixa->create([
                'user_id' => auth()->user()->id,
                'valor' => $parcela->valor,
                'parcela_id' => $parcela->id,
                'descricao' => "Estorno da parcela: ".$parcela->nro_parcela."/".count($parcela->conta->parcelas)." do título ".$parcela->conta->titulo .
                    "\r\nPessoa: " . $parcela->conta->pessoa->nome_documento_completo,
                'movimentacao' => $parcela->conta->tipo_operacao == 'R' ? 'SAIDA' : 'ENTRADA'
            ]);

			DB::commit();
			return response()->json(['erro' => 0, 'mensagem' => 'Parcela estornada com sucesso!']);
		} catch (\Exception $e) {
			DB::rollback();
			return response()->json(['erro' => 1, 'mensagem' => $e->getMessage()]);
		}
	}
}
