<?php

namespace app\services;

use app\classes\Cliente;
use app\exceptions\NaoAutorizadoException;

class ClienteService extends Service {

    protected function validar( $categoria, array &$erro = [] ){
    }

    public function autenticar( string $email, string $senha ){
        $cliente = $this->obterComEmail( $email );
        if( ! $cliente instanceof Cliente ){
            throw new NaoAutorizadoException( 'Email não encontrado.' );
        }

        if( ! password_verify( $cliente->getSenha(), $senha ) ){
            throw new NaoAutorizadoException( 'Email ou senha inválidos.' );
        }

        $token = 'gera token';
        return $token;
    }

    public function obterComEmail( string $email ){

    }
}