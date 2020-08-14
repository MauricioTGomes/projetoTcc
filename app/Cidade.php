<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cidade extends Model
{
    protected $table = 'cidade';

    protected $fillable = ['estado_id', 'codigo', 'nome'];

    protected $appends = ["nome_completo"];

    public function estado() {
        return $this->belongsTo(Estado::class);
    }

    public function getNomeCompletoAttribute() {
        return $this->nome . ' - ' . $this->estado->uf;
    }
}