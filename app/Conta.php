<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Webpatser\Uuid\Uuid;

class Conta extends Model {

	protected $fillable = [
		'pessoa_id',
		'pedido_id',
		'user_id',
		'titulo',
		'data_emissao',
		'vlr_total',
		'vlr_restante',
		'tipo_operacao',
		'qtd_dias'
	];

	protected $table = 'conta_receber_pagar';

	public function setTituloAttribute($value) {
		if ($value == '' || $value == null) {
			return $this->attributes['titulo'] = strtoupper(substr(Uuid::generate(), 0, 7));
		}
		return $this->attributes['titulo'] = $value;
	}

	public function setVlrTotalAttribute($value) {
		if (str_contains($value, ',')) {
			return $this->attributes['vlr_total'] = formatValueForMysql($value);
		}
		return $this->attributes['vlr_total'] = $value;
	}

	public function setVlrRestanteAttribute($value) {
		if (str_contains($value, ',')) {
			return $this->attributes['vlr_restante'] = formatValueForMysql($value);
		}
		return $this->attributes['vlr_restante'] = $value;
	}

	public function pessoa() {
		return $this->belongsTo(Pessoa::class );
	}

	public function parcelas() {
		return $this->hasMany(Parcela::class );
	}

	public function pedido() {
		//return $this->hasOne(Pedido::class , 'pedido_id');
	}

	public function parcelasPagas() {
		return $this->hasMany(Parcela::class )->where('baixada', '1');
	}

	public function getDataEmissaoAttribute($value) {
		return (new Carbon($value))->format('d/m/Y');

	}

	public function setDataEmissaoAttribute($value) {
		if (strlen($value) > 0) {
			try {
				$this->attributes['data_emissao'] = Carbon::createFromFormat('d/m/Y', $value);
			} catch (\Exception $e) {
				$this->attributes['data_emissao'] = date('Y-m-d');
			}
		} else {
			return null;
		}
	}
}