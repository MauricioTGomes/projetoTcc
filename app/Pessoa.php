<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    protected $appends = ['mostrar_detalhe', "nome_completo", "nome_documento_completo", "documento_completo", 'nome_cidade_completo'];

    public function deletePessoa($idPessoa) {
        return DB::delete('delete from pessoa where id = ?', [$idPessoa]);
    }

    public function insertPessoa($array) {
        return DB::update("insert into pessoa (cidade_id, fantasia, nome, razao_social, cpf, cnpj, ie, rg, cep, email, endereco, endereco_nro, bairro, complemento, fone, ativo, cliente, tipo, fornecedor) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '0')", [$array['cidade_id'], $array['fantasia'], $array['nome'], $array['razao_social'], $array['cpf'], $array['cnpj'], $array['ie'], $array['rg'], $array['cep'], $array['email'], $array['endereco'], $array['endereco_nro'], $array['bairro'], $array['complemento'], $array['fone'], $array['ativo'], $array['cliente'], $array['tipo']]);
    }

    public function updatePessoa($array) {
        return DB::update("update pessoa set cidade_id = ?, fantasia = ?, nome = ?, razao_social = ?, cpf = ?, cnpj = ?, ie = ?, rg = ?, cep = ?, email = ?, endereco = ?, endereco_nro = ?, bairro = ?, complemento = ?, fone = ?, ativo = ?, cliente = ?, tipo = ? where id = ?", [$array['cidade_id'], $array['fantasia'], $array['nome'], $array['razao_social'], $array['cpf'], $array['cnpj'], $array['ie'], $array['rg'], $array['cep'], $array['email'], $array['endereco'], $array['endereco_nro'], $array['bairro'], $array['complemento'], $array['fone'], $array['ativo'], $array['cliente'], $array['tipo'], $array['id']]);
    }

    public function find($id) {
        $pessoa = DB::select("select pessoa.*, concat(cidade.nome, ' - ', estado.uf) as nome_cidade_completo from pessoa
                left join cidade on cidade.id = pessoa.cidade_id
                left join estado on estado.id = cidade.estado_id
                where pessoa.id = ?", [$id])[0];
        $pessoa->contas = DB::select("select * from conta_receber_pagar where pessoa_id = ?", [$id]);
        $pessoa->pedidos = DB::select("select * from pedido where pessoa_id = ?", [$id]);
        return $pessoa;
    }

    public function listagem($inativo = false) {
        return DB::select("
            select
                pessoa.*, if(pessoa.nome is null, concat(pessoa.fantasia, ' (' ,pessoa.razao_social, ')'), pessoa.nome) as nome_completo,
                if(pessoa.cpf is null, pessoa.cnpj, pessoa.cpf) as documento_completo, concat(cidade.nome, ' - ', estado.uf) as nome_cidade_completo
            from pessoa
                left join cidade on cidade.id = pessoa.cidade_id
                left join estado on estado.id = cidade.estado_id
            where ativo = ?
        ", [$inativo ? '0' : '1']);
    }
}
