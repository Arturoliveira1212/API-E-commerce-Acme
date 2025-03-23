<?php

namespace app\controllers;

use app\classes\Cliente;
use app\controllers\Controller;
use app\services\ClienteService;
use app\classes\http\HttpStatusCode;

class ClienteController extends Controller {
    protected function criar( array $dados ){
        $cliente = new Cliente();
        $camposSimples = [ 'id', 'nome', 'email', 'cpf', 'senha' ];
        $this->povoarSimples( $cliente, $camposSimples, $dados );

        $camposDateTime = [ 'dataNascimento' ];
        $this->povoarDateTime( $cliente, $camposDateTime, $dados );

        return $cliente;
    }

    public function login( array $dados ){
        [ 'email' => $email, 'senha' => $senha ] = $dados;

        /** @var ClienteService */
        $clienteService = $this->getService();
        /** @var TokenJWT */
        $tokenJWT = $clienteService->autenticar( $email, $senha );

        return $this->resposta( HttpStatusCode::OK, [
            'message' => 'Cliente autenticado com sucesso.',
            'data' => [
                'Token' => $tokenJWT->codigo(),
                'Duração' => $tokenJWT->validadeTokenFormatada()
            ]
        ] );
    }
}