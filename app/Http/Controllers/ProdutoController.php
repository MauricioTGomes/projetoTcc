<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Produto;
use Illuminate\Support\Facades\DB;

class ProdutoController extends Controller
{

    public function __contruct() {}


    public function gravar(Request $request, Produto $produtoModel) {
        try {
            DB::beginTransaction();
            $produtoModel->insertProduto($request->all());
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
            $produtoModel->deleteProduto($idProduto);
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
            $produtoModel->updateProduto($request->all());
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
