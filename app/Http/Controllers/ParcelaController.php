<?php

namespace App\Http\Controllers;

use App\Conta;
use App\MovimentacaoCaixa;
use App\Parcela;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Exception;

class ParcelaController extends Controller {

	private $parcelaModel;
	private $contaModel;
	private $movimentacaoCaixa;

	public function __construct(Parcela $parcelaModel, Conta $contaModel) {
		$this->parcelaModel      = $parcelaModel;
		$this->contaModel        = $contaModel;
		//$this->movimentacaoCaixa = $movimentacaoCaixa;
	}

	public function baixarParcela(Request $request) {
		try {
			DB::beginTransaction();
			$input    = $request->all();
			$desconto = formatValueForMysql($input['valor_desconto']);
			$parcela  = $this->parcelaModel->find($input['id']);
			if ($desconto > $parcela->valor) {
				throw new Exception("Valor de desconto maior que o valor de desconto");
			}
			$valorPago                 = $parcela->valor-$desconto;
			$parcela->valor_pago       = $valorPago;
			$parcela->data_recebimento = Carbon::now()->format('Y-m-d');
			$parcela->valor_desconto   = $desconto;
			$parcela->baixada          = 1;
			$parcela->save();

			$valorRestanteConta           = formatValueForMysql($parcela->conta->vlr_restante)-$parcela->valor;
			$parcela->conta->vlr_restante = formatValueForUser($valorRestanteConta);
			$parcela->conta->save();

			$descricao = "Recebimento parcela: ".$parcela->nro_parcela."/".$parcela->conta->qtd_parcelas." do título ".$parcela->conta->titulo;
			if ($parcela->conta->tipo_operacao == 'P') {
				$estornado = '1';
				$descricao = "Pagamento parcela: ".$parcela->nro_parcela."/".$parcela->conta->qtd_parcelas." do título ".$parcela->conta->titulo;
			}

			$this->movimentacaoCaixa->create([
					'user_id'        => Auth::user()->id,
					'valor_total'    => $parcela->valor,
					'parcela_id'     => $parcela->id,
					'valor_desconto' => $parcela->valor_desconto,
					'valor_pago'     => $parcela->valor_pago,
					'descricao'      => $descricao,
					'estornado'      => '0',
					'data_pagamento' => Carbon::now(),
					'tipo'           => $parcela->conta->tipo_operacao
				]);

			DB::commit();
			return response()->json(['erro' => 0, 'msg' => 'Baixa realizada com sucesso!', 'tipo' => $parcela->conta->tipo_operacao == 'P'?'pagar':'receber']);
		} catch (\Exception $e) {
			DB::rollback();
			return response()->json(['erro' => 1, 'msg' => $e->getMessage()]);
		}
	}

	public function estornoParcela($id) {
		try {
			DB::beginTransaction();
			$parcela                   = $this->parcelaModel->find($id);
			$parcela->valor_pago       = '0.00';
			$parcela->valor_desconto   = '0.00';
			$parcela->data_recebimento = null;
			$parcela->baixada          = 0;
			$parcela->save();

			$valorRestanteConta           = formatValueForMysql($parcela->conta->vlr_restante)+$parcela->valor;
			$parcela->conta->vlr_restante = formatValueForUser($valorRestanteConta);
			$parcela->conta->save();

			$this->movimentacaoCaixa->create([
					'user_id'        => Auth::user()->id,
					'valor_total'    => $parcela->valor,
					'valor_desconto' => $parcela->valor_desconto,
					'valor_pago'     => $parcela->valor_pago,
					'descricao'      => "Estorno da parcela: ".$parcela->nro_parcela."/".$parcela->conta->qtd_parcelas." do título ".$parcela->conta->titulo,
					'estornado'      => '1',
					'parcela_id'     => $parcela->id,
					'tipo'           => $parcela->conta->tipo_operacao
				]);

			DB::commit();
			return response()->json(['erro' => 0, 'msg' => 'Parcela estornada com sucesso!']);
		} catch (\Exception $e) {
			DB::rollback();
			return response()->json(['erro' => 1, 'msg' => $e->getMessage()]);
		}
	}
}