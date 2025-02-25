<?php

namespace app\classes\jwt;

class TokenJWT {
    private string $codigo;
    private int $tempoExpiracaoEmSegundos;

    public function __construct( string $codigo, int $tempoExpiracaoEmSegundos ){
        $this->codigo = $codigo;
        $this->tempoExpiracaoEmSegundos = $tempoExpiracaoEmSegundos;
    }

    public function codigo(){
        return $this->codigo;
    }

    public function tempoExpiracaoEmSegundos(){
        return $this->tempoExpiracaoEmSegundos;
    }
}