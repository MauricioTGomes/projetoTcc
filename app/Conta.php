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
		'tipo_operacao',
		'vlr_total',
		'vlr_restante',
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
		return $this->attributes['vlr_total'] = formatValueForMysql($value);
	}

	public function setVlrRestanteAttribute($value) {
		return $this->attributes['vlr_restante'] = formatValueForMysql($value);
	}

    public function getVlrTotalAttribute($value) {
        return formatValueForUser($value);
    }

    public function getVlrRestanteAttribute($value) {
        return formatValueForUser($value);
    }

	public function pessoa() {
		return $this->belongsTo(Pessoa::class );
	}

	public function parcelas() {
		return $this->hasMany(Parcela::class );
	}

	public function pedido() {
		return $this->hasOne(Pedido::class , 'pedido_id');
	}

	public function parcelasPagas() {
		return $this->hasMany(Parcela::class )->where('baixada', '1');
	}

    public function parcelasAbertas() {
        return $this->hasMany(Parcela::class )->where('baixada', '0');
    }

	public function getDataEmissaoAttribute($value) {
		return (new Carbon($value))->format('d/m/Y');

	}

	public function setDataEmissaoAttribute($value) {
		try {
			$this->attributes['data_emissao'] = Carbon::createFromFormat('d/m/Y', $value);
		} catch (\Exception $e) {
			$this->attributes['data_emissao'] = date('Y-m-d');
		}
	}

	public function getContasListagem($tipo = 'R', $valorZero = false) {
	    return $this->newQuery()
            ->join('pessoa', 'pessoa.id', '=', 'conta_receber_pagar.pessoa_id')
            ->where('tipo_operacao', $tipo)
            ->where('vlr_restante', $valorZero ? '<=' : '>', '0.00')
            ->with('pessoa', 'parcelas', 'parcelasPagas', 'parcelasAbertas')
            ->select(DB::raw("conta_receber_pagar.*, if(pessoa.tipo = 'JURIDICO', CONCAT(pessoa.razao_social, ' (', pessoa.cnpj, ')'), CONCAT(pessoa.nome, ' (', pessoa.cpf, ')')) as nome_pessoa"))->get();
    }
}
