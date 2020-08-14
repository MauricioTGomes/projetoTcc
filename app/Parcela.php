<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Parcela extends Model {

	protected $fillable = [
		'conta_id',
		'nro_parcela',
        'baixada',
		'valor',
		'valor_original',
		'data_vencimento',
		'data_pagamento',
	];

	protected $table = 'parcela_receber_pagar';

	protected $dates = ['data_pagamento', 'data_vencimento', 'updated_at', 'created_at'];

	public function setDataVencimentoAttribute($value) {
		try {
			$this->attributes['data_vencimento'] = Carbon::createFromFormat('Y-m-d', $value);
		} catch (\Exception $e) {
			$this->attributes['data_vencimento'] = date('Y-m-d');
		}
	}

	public function getDataVencimentoAttribute($value) {
		try {
			return (new Carbon($value))->format('d/m/Y');
		} catch (\Exception $e) {
			return $value;
		}
	}

	public function getDatapagamentoAttribute($value) {
		return (new Carbon($value))->format('d/m/Y');
	}

	public function setDatapagamentoAttribute($value) {
		try {
			$this->attributes['data_pagamento'] = Carbon::createFromFormat('d/m/Y', $value);
		} catch (\Exception $e) {
			$this->attributes['data_pagamento'] = date('Y-m-d');
		}
	}

	public function setValorAttribute($value) {
		return $this->attributes['valor'] = formatValueForMysql($value);
	}

	public function setValorOriginalçAttribute($value) {
		return $this->attributes['valor_original'] = formatValueForMysql($value);
	}

	public function conta() {
		return $this->belongsTo(Conta::class , 'conta_id');
    }
    
    public function getTotalDia($tipo, $dia) {
		$query = $this->newQuery();
		$query->join('contas_receber_pagar as conta', 'conta.id', '=', 'parcelas_receber_pagar.conta_id')
		      ->where('tipo_operacao', $tipo)
		      ->where('data_vencimento', '<=', DB::raw("'$dia'"))
		      ->where('baixada', '0');
		return $query->with('conta')->get();
	}

}