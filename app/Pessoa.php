<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pessoa extends Model
{

    protected $table = 'pessoa';

    protected $fillable = [
        'cidade_id',
        'fantasia',
        'nome',
        'razao_social',
        'cpf',
        'cnpj',
        'ie',
        'rg',
        'cep',
        'email',
        'endereco',
        'endereco_nro',
        'bairro',
        'complemento',
        'fone',
        'ativo',
        'cliente',
        'fornecedor',
        'tipo'
    ];

    protected $appends = ["nome_completo", "nome_documento_completo", "documento_completo", 'nome_cidade_completo'];

    public function cidade() {
		return $this->hasOne(Cidade::class , 'id', 'cidade_id');
	}

    public function getNomeCompletoAttribute() {
        return $this->tipo == 'FISICO' ? $this->nome : $this->fantasia . ' (' . $this->razao_social . ')';
    }

    public function getDocumentoCompletoAttribute() {
        return $this->tipo == 'FISICO' ? $this->cpf : $this->cnpj;
    }

    public function getNomeCidadeCompletoAttribute() {
        return $this->cidade->nome . ' - ' . $this->cidade->estado->uf;
    }

    public function getNomeDocumentoCompletoAttribute() {
        return $this->tipo == 'FISICO' ? $this->nome . ' (' . $this->cpf . ')' : $this->fantasia . ' (' . $this->cnpj . ')';
    }

    public function setFornecedorAttribute($value) {
        return $this->attributes['fornecedor'] = $value ? '1' : '0';
    }

    public function setClienteAttribute($value) {
        return $this->attributes['cliente'] = $value ? '1' : '0';
    }


    public function setAtivoAttribute($value) {
        return $this->attributes['ativo'] = $value['value'];
    }

    public function setTipoAttribute($value) {
		return $this->attributes['tipo'] = $value['value'];
	}

    public function buscaPessoasPesquisa($parametro = null) {
		$query = $this->newQuery();

		if ($parametro == null) {
			return $query->paginate(10);
		}

		$query->where(function ($q) use ($parametro) {
				$q->where('ativo', '1')
					->where('nome', 'like', "%$parametro%")
				->orWhere('cpf', 'like', "%$parametro%");
			});

		return $query->get();
    }
}