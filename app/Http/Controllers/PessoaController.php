<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Pessoa;

class PessoaController extends Controller
{

    public function __contruct() {}


    public function gravar(Request $request) {
        try {
            DB::beginTransaction();
            Pessoa::create($request->all());
            DB::commit();
            return response()->json(['erro' => false, 'mensagem' => 'Pessoa cadastrada com sucesso.']);
        } catch(\Exception $e) {
            DB::rollback();
            return response()->json(['erro' => true, 'mensagem' => 'Erro ao cadastrar pessoa ' . $e->getMessage()]);
        }
    }

    public function excluir($idPessoa, Pessoa $pessoaModel) {
        try {
            DB::beginTransaction();
            $pessoa = $pessoaModel->find($idPessoa);
			//if (!is_null($pessoa->pedidos->first()) || !is_null($pessoa->contas->first())) {
			//	throw new Exception("pessoa com movimentação financeira, cancele e apague as contas e vendas antes de continuar");
			//}
			$pessoa->delete();
            DB::commit();
            return response()->json(['erro' => false, 'mensagem' => 'Pessoa eliminada com sucesso.']);
        } catch(\Exception $e) {
            DB::rollback();
            return response()->json(['erro' => true, 'mensagem' => 'Erro ao eliminar pessoa ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, Pessoa $pessoaModel) {
        try {
            DB::beginTransaction();
            $input = $request->all();
			$pessoa = $pessoaModel->find($input['id']);
			$pessoa->update($input);
			DB::commit();
			return response()->json(['erro' => false, 'mensagem' => 'Pessoa alterada com sucesso.']);
		} catch (\Exception $e) {
			DB::rollback();
			return response()->json(['erro' => true, 'mensagem' => 'Erro ao alterar pessoa ' . $e->getMessage()]);
		}
    }

    public function getPessoa($idPessoa, Pessoa $pessoaModel) {
        return \response()->json($pessoaModel->find($idPessoa));
    }

    public function getPessoas(Pessoa $pessoaModel) {
        return response()->json($pessoaModel->all());
    }
}
