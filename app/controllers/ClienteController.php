<?php

namespace app\controllers;

use app\controllers\Controller;
use app\services\ClienteService;
use app\classes\http\HttpStatusCode;

class ClienteController extends Controller {

    protected function criar( array $corpoRequisicao ){

    }

    public function novo( array $corpoRequisicao ){

    }

    public function login( array $corpoRequisicao ){
        [ 'email' => $email, 'senha' => $senha ] = $corpoRequisicao;

        /** @var ClienteService */
        $clienteService = $this->getService();
        $token = $clienteService->autenticar( $email, $senha );

        return $this->resposta( HttpStatusCode::OK, [
            'Token' => $token
        ] );
    }
}