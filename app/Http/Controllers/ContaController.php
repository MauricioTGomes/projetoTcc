<?php

namespace App\Http\Controllers;

use App\Conta;
use App\Parcela;
use App\Pessoa;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContaController extends Controller {

	private $contaModel;
	private $pessoaModel;
	private $parcelaModel;

	public function __construct(Conta $contaModel, Pessoa $pessoaModel, Parcela $parcelaModel) {
		$this->contaModel   = $contaModel;
		$this->parcelaModel = $parcelaModel;
		$this->pessoaModel  = $pessoaModel;
	}

	public function getContas(Request $request) {
		$contas = $this->contaModel->getContasListagem($request->input('tipo'));
		return response()->json($contas);
	}

	public function calculaParcela(Request $request) {
		$vlrTotal    = formatValueForMysql($request->input('vlr_total'));
		$qtdDias     = $request->input('qtd_dias');
		$qtdParcelas = $request->input('qtd_parcelas');
		$primeiraCobranca = Carbon::createFromFormat('Y-m-d', $request->input('primeira_cobranca'));

		$vlr_parcela   = $vlrTotal/$qtdParcelas;
		$arrayParcelas = [];
		$somaParcelas  = 0;

		for ($i = 1; $i <= $qtdParcelas; $i++) {
			if ($i == 1) {
				$dataParcela = $primeiraCobranca;
			} else {
				$dataParcela = $primeiraCobranca->addDays($qtdDias);
			}
			array_push($arrayParcelas, [
					'data_vencimento' => $dataParcela->format('Y-m-d'),
					'valor'           => number_format(round($vlr_parcela, 2), 2, ',', '.'),
					'nro_parcela'     => $i]);
			$somaParcelas += round($vlr_parcela, 2);
		}

		$diferenca = number_format($vlrTotal-$somaParcelas, 2);

		if ($diferenca > 0) {
			$arrayParcelas[$qtdParcelas-1]['valor'] = number_format(
				formatValueForMysql($arrayParcelas[$qtdParcelas-1]['valor'])+$diferenca, 2, ',', '.');
		}
		if ($diferenca < 0) {
			$arrayParcelas[$qtdParcelas-1]['valor'] = number_format(
				formatValueForMysql($arrayParcelas[$qtdParcelas-1]['valor'])-($diferenca*-1), 2, ',', '.');
		}

		return $arrayParcelas;
	}

	public function gravar(Request $request) {
		try {
			DB::beginTransaction();
			if ($request->get('parcelas') == 0) {
				throw new \Exception("Favor calcular as parcelas antes de continuar");
			}

			$input = $request->all();
			$input['user_id'] = auth()->user()->id;
			$input['vlr_restante'] = $input['vlr_total'];
			$input['data_emissao'] = Carbon::now();
			$conta = Conta::create($input);
			$this->gravaParcelas($input['parcelas'], $conta->id);
			DB::commit();
			return response()->json(['erro' => false, 'mensagem' => 'Conta lançada com sucesso.']);
		} catch (\Exception $e) {
			DB::rollback();
			return response()->json(['erro' => true, 'mensagem' => 'Erro ao lançar conta ' . $e]);
		}
	}

	private function gravaParcelas($parcelas, $contaId) {
		foreach ($parcelas as $index => $parcela) {
            $parcela['valor_original'] = $parcela['valor'];
			$parcela['conta_id'] = $contaId;
			Parcela::create($parcela);
		}
	}

	public function update(Request $request) {
		try {
			DB::beginTransaction();
			if ($request->get('parcelas') == 0) {
				throw new \Exception("Favor calcular as parcelas antes de continuar");
			}
            $input = $request->all();
			$conta = $this->contaModel->find($input['id']);
			$conta->parcelas()->delete();
			$input['vlr_restante'] = $input['vlr_total'];
			$conta->update($input);
			$this->gravaParcelas($request->get('parcelas'), $conta->id);
			DB::commit();
			return response()->json(['erro' => false, 'mensagem' => 'Conta lançada com sucesso.']);
		} catch (\Exception $e) {
			DB::rollback();
			return response()->json(['erro' => true, 'mensagem' => 'Erro ao lançar conta ' . $e->getMessage()]);
		}
	}

	public function getConta($id) {
	    $conta = $this->contaModel->find($id)->load('pessoa');
		return response()->json($conta);
	}

	public function excluir($id) {
		$conta = $this->contaModel->find($id);
		try {
			foreach ($conta->parcelas as $parcela) {
				if ($parcela->baixada == 1) {
					throw new \Exception("Conta com movimentação financeira");
				}
			}
			$conta->delete();
			return response()->json(['erro' => false, 'mensagem' => 'Conta excluida com sucesso.']);
		} catch (\Exception $e) {
			DB::rollback();
			return response()->json(['erro' => true, 'mensagem' => 'Erro excluir conta ' . $e->getMessage()]);
		}
	}
}
