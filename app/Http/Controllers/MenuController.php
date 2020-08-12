<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cidade;

class MenuController extends Controller
{

    public function __contruct() {}

    public function getCidadesSelect(Cidade $cidade) {
        return response()->json($cidade->getSelect());
    }
}
