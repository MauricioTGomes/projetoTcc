<?php

use App\Conta;
use App\Parcela;
use App\Pessoa;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

use League\Flysystem\Exception;

class ContaController extends Controller {

	private $contaModel;
	private $pessoaModel;
	private $parcelaModel;

	public function __construct(Conta $contaModel, Pessoa $pessoaModel, Parcela $parcelaModel) {
		$this->contaModel   = $contaModel;
		$this->parcelaModel = $parcelaModel;
		$this->pessoaModel  = $pessoaModel;
	}

	private function formataData($data) {
		return $data == "" ? null : Carbon::createFromFormat('d/m/Y', $data)->format('Y-m-d');
	}

	public function buscaContas(Request $request) {
		$contas = $this->contaModel->newQuery()->where('tipo_operacao', $request->input('tipo'))->where('vlr_restante', '>', '0.00')->with('pessoa', 'parcelas', 'parcelasPagas')->get();
		return response()->json($contas);
	}

	public function calculaParcela(Request $request) {
		$vlrTotal    = formatValueForMysql($request->input('vlr_total'));
		$qtdDias     = $request->input('qtd_dias');
		$qtdParcelas = $request->input('qtd_parcelas');
		$dataEmissao = Carbon::createFromFormat('d/m/Y', $request->input('data_emissao'));

		$vlr_parcela   = $vlrTotal/$qtdParcelas;
		$arrayParcelas = [];
		$somaParcelas  = 0;

		for ($i = 1; $i <= $qtdParcelas; $i++) {
			$dataParcela = $dataEmissao->addDays($qtdDias);
			array_push($arrayParcelas, [
					'data_vencimento' => $dataParcela->format('d/m/Y'),
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

	public function gravar(ContaRequest $request) {
		try {
			DB::beginTransaction();
			if ($request->get('array_parcela') == 0) {
				throw new Exception("Favor calcular as parcelas antes de continuar");

			}

			$input                 = $request->all();
			$input['user_id']      = Auth::user()->id;
			$input['vlr_restante'] = $input['vlr_total'];
			$conta                 = Conta::create($input);
			$this->gravaParcelas($input['array_parcela'], $conta->id);
			DB::commit();
			return redirect()->route('contas.'.($request->get('tipo_operacao') == 'R'?'receber':'pagar').'.listar'.
				'')
				->with(['sucesso' => "Sucesso ao lançar conta", 'conta' => $conta]);
		} catch (\Exception $e) {
			DB::rollback();
			return back()->with('erro', 'Erro ao lançar conta'."\n".$e->getMessage());
		}
	}

	private function gravaParcelas($parcelas, $contaId) {
		foreach ($parcelas as $index => $parcela) {
			$parcela['conta_id'] = $contaId;
			Parcela::create($parcela);
		}
	}

	public function update($id, Request $request) {
		try {
			DB::beginTransaction();
			if ($request->get('array_parcela') == 0) {
				throw new Exception("Favor calcular as parcelas antes de continuar");

			}
			$conta = $this->contaModel->find($id);
			foreach ($conta->parcelas as $parcela) {
				$parcela->delete();
			}
			$conta->load('parcelas');
			$input                 = $request->all();
			$input['vlr_total']    = formatValueForMysql($input['vlr_total']);
			$input['vlr_restante'] = $input['vlr_total'];
			$conta->update($input);
			$this->gravaParcelas($request->get('array_parcela'), $conta->id);
			DB::commit();
			return redirect()->route('contas.'.($request->get('tipo_operacao') == 'R'?'receber':'pagar').'.listar')
				->with('sucesso', "Conta alterada com sucesso.");
		} catch (\Exception $e) {
			DB::rollback();
			return back()->with('erro', "Não foi possível salvar alterações"."\n".$e->getMessage());
		}
	}

	public function deletar($id) {
		$conta = $this->contaModel->find($id);
		$tipo  = $conta->tipo_operacao == 'R'?'receber':'pagar';
		try {
			foreach ($conta->parcelas as $parcela) {
				if ($parcela->baixada == 1) {
					throw new \Exception("Conta com movimentação financeira");

				}
			}
			$conta->delete();
			return response()->json(['erro' => 0, 'msg' => 'Sucesso ao eliminar conta!']);
		} catch (\Exception $e) {
			DB::rollback();
			return response()->json(['erro' => 1, 'msg' => $e->getMessage()]);
		}
	}
}