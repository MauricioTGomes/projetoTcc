<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pessoa;
use Illuminate\Support\Facades\DB;

class PessoaController extends Controller
{

    public function __contruct() {}

    public function gravar(Request $request, Pessoa $pessoaModel) {
        try {
            DB::beginTransaction();
            $pessoaModel->insertPessoa($request->all());
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
            if (isset($pessoa->pedidos[0]) || isset($pessoa->contas[0])) {
				throw new \Exception("pessoa com movimentação financeira, cancele e apague as contas e vendas antes de continuar");
			}
			$pessoaModel->deletePessoa($idPessoa);
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
            $pessoaModel->updatePessoa($input);
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

    public function getPessoas(Request $request, Pessoa $pessoaModel) {
        return response()->json($pessoaModel->listagem($request->input('inativo')));
    }
}
