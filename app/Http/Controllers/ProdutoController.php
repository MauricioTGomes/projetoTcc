<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Produto;
use Illuminate\Support\Facades\DB;

class ProdutoController extends Controller
{

    public function __contruct() {}


    public function gravar(Request $request) {
        try {
            DB::beginTransaction();
            Produto::create($request->all());
            DB::commit();
            return response()->json(['erro' => false, 'mensagem' => 'Produto cadastrada com sucesso.']);
        } catch(\Exception $e) {
            DB::rollback();
            return response()->json(['erro' => true, 'mensagem' => 'Erro ao cadastrar produto ' . $e->getMessage()]);
        }
    }

    public function excluir($idProduto, Produto $produtoModel) {
        try {
            DB::beginTransaction();
            $produto = $produtoModel->find($idProduto);
			//if (!is_null($pessoa->pedidos->first()) || !is_null($pessoa->contas->first())) {
			//	throw new Exception("pessoa com movimentação financeira, cancele e apague as contas e vendas antes de continuar");
			//}
			$produto->delete();
            DB::commit();
            return response()->json(['erro' => false, 'mensagem' => 'Produto eliminada com sucesso.']);
        } catch(\Exception $e) {
            DB::rollback();
            return response()->json(['erro' => true, 'mensagem' => 'Erro ao eliminar produto ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, Produto $produtoModel) {
        try {
            DB::beginTransaction();
            $input = $request->all();
			$produto = $produtoModel->find($input['id']);
			$produto->update($input);
			DB::commit();
			return response()->json(['erro' => false, 'mensagem' => 'Produto alterada com sucesso.']);
		} catch (\Exception $e) {
			DB::rollback();
			return response()->json(['erro' => true, 'mensagem' => 'Erro ao alterar produto ' . $e->getMessage()]);
		}
    }

    public function getProduto($idProduto, Produto $produtoModel) {
        return \response()->json($produtoModel->find($idProduto));
    }

    public function getProdutos(Request $request, Produto $produtoModel) {
        return response()->json($produtoModel->listagem($request->input('inativo')));
    }
}
