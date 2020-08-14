<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cidade;

class MenuController extends Controller
{

    public function __contruct() {}

    public function getCidade(Request $request, Cidade $cidadeModel) {
        $input = $request->all();
        $cidade = $cidadeModel->newQuery()->where($input['campo'], $input['valor'])->get()->first();
        return response()->json($cidade);
    }
}
