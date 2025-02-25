<?php

namespace app\controllers;

use app\controllers\Controller;
use app\classes\http\HttpStatusCode;

class AdministradorController extends Controller {

    protected function criar( array $corpoRequisicao ){

    }

    public function login( array $corpoRequisicao ){
        [ 'email' => $email, 'senha' => $senha ] = $corpoRequisicao;

        /** @var AdministradorService */
        $administradorService = $this->getService();
        $token = $administradorService->autenticar( $email, $senha );

        return $this->resposta( HttpStatusCode::OK, [
            'Token' => $token
        ] );
    }
}