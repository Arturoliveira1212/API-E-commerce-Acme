<?php

namespace app\services;

use app\classes\Administrador;
use app\classes\jwt\AutenticacaoJWT;
use app\classes\jwt\TokenJWT;
// use app\classes\TokenJWT as ClassesTokenJWT;
use app\classes\Validador;
use app\exceptions\NaoAutorizadoException;
// use App\Services\TokenJWT;
use Exception;
// use Twig\Token;

class AdministradorService extends Service {

    protected function validar( $administrador, array &$erro = [] ){
        if( ! Validador::validarEmail( $administrador->getEmail() ) ){
            $erros[] = 'E-mail inválido.';
        }
    }

    public function autenticar( string $email, string $senha ){
        $administrador = $this->obterComEmail( $email );
        if( ! $administrador instanceof Administrador ){
            throw new NaoAutorizadoException( 'E-mail não encontrado.' );
        }

        if( ! password_verify( $administrador->getSenha(), $senha ) ){
            throw new NaoAutorizadoException( 'E-mail ou senha inválidos.' );
        }

        $autenticacaoJWT = new AutenticacaoJWT();
        $tokenJWT = $autenticacaoJWT->gerarToken(
            $administrador->getId(),
            $administrador->getNome(),
            'admin'
        );

        if( ! $tokenJWT instanceof TokenJWT ){
            throw new Exception( 'Houve um erro ao gerar o token de acesso.' );
        }

        return $tokenJWT;
    }

    public function obterComEmail( string $email ){

    }
}