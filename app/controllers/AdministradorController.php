<?php

namespace app\controllers;

use app\classes\Administrador;
use app\core\HttpStatusCode;
use app\exceptions\NaoAutorizadoException;
use app\services\AdministradorService;

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