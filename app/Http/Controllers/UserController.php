<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller {

	private $userModel;

	public function __construct(User $user) {
		$this->userModel = $user;
	}

	public function getUsuarios() {
		$users = $this->userModel->all();
		return response()->json($users);
	}

	public function gravar(Request $request) {
		try {
			DB::beginTransaction();
			$input = $request->all();
			$user = $this->userModel->getEmail($input['email']);
			if (!is_null($user)) {
			    throw new \Exception("E-mail já cadastrado");
            }
			$input['password'] = bcrypt($input['password']);
			$this->userModel->create($input);
			DB::commit();
			return response()->json(['erro' => false, 'mensagem' => 'Usuário criado com sucesso.']);
		} catch (\Exception $e) {
			DB::rollback();
			return response()->json(['erro' => true, 'mensagem' => 'Erro ao cadastrar usuário ' . $e->getMessage()]);
		}
	}

	public function update(Request $request) {
		try {
			DB::beginTransaction();
            $input = $request->all();
            $user = $this->userModel->getEmail($input['email']);
            if (!is_null($user) && $user->id !== $input['id']) {
                throw new \Exception("E-mail já cadastrado");
            }
            $input['password'] = bcrypt($input['password']);
			$user = $this->userModel->find($input['id']);
			$user->update($input);
			DB::commit();
			return response()->json(['erro' => false, 'mensagem' => 'Usuário cadastrado com sucesso.']);
		} catch (\Exception $e) {
			DB::rollback();
			return response()->json(['erro' => true, 'mensagem' => 'Erro ao cadastrar usuário ' . $e->getMessage()]);
		}
	}

	public function getUsuario($id) {
	    $user = $this->userModel->find($id);
		return response()->json($user);
	}

	public function excluir($id) {
		try {
	        $user = $this->userModel->find($id);
            $user->delete();
			return response()->json(['erro' => false, 'mensagem' => 'Usuário excluido com sucesso.']);
		} catch (\Exception $e) {
			DB::rollback();
			return response()->json(['erro' => true, 'mensagem' => 'Erro ao excluir usuário ' . $e->getMessage()]);
		}
	}
}
