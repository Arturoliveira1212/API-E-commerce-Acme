<?php

namespace app\controllers;

use app\classes\Administrador;
use app\controllers\Controller;
use app\classes\http\HttpStatusCode;
use app\classes\jwt\TokenJWT;
use app\exceptions\NaoEncontradoException;
use app\services\AdministradorService;

class AdministradorController extends Controller {

    protected function criar( array $corpoRequisicao ){
        $administrador = new Administrador();
        $camposSimples = [ 'nome', 'email', 'senha' ];
        $this->povoarSimples( $administrador, $camposSimples, $corpoRequisicao );

        return $administrador;
    }

    public function novo( array $corpoRequisicao ){
        $administrador = $this->criar( $corpoRequisicao );
        $this->getService()->salvar( $administrador );

        return $this->resposta( HttpStatusCode::CREATED, [
            'mensagem' => "Administrador cadastrado com sucesso"
        ] );
    }

    public function login( array $corpoRequisicao ){
        [ 'email' => $email, 'senha' => $senha ] = $corpoRequisicao;

        /** @var AdministradorService */
        $administradorService = $this->getService();
        /** @var TokenJWT */
        $tokenJWT = $administradorService->autenticar( $email, $senha );

        return $this->resposta( HttpStatusCode::OK, [
            'Token' => $tokenJWT->codigo(),
            'Duração' => $tokenJWT->validadeTokenFormatada()
        ] );
    }

    public function editar( array $corpoRequisicao, $args ){
        $id = intval( $args['id'] );

        $administrador = $this->getService()->obterComId( $id );
        if( ! $administrador instanceof Administrador ){
            throw new NaoEncontradoException( 'Administrador não encontrado.' );
        }

        $administrador = $this->criar( $corpoRequisicao );
        $administrador->setId( $id );
        $this->getService()->salvar( $administrador );

        return $this->resposta( HttpStatusCode::OK, [
            'mensagem' => 'Administrador atualizado com suceso.'
        ] );
    }

    public function obterTodos( array $corpoRequisicao, $args, array $parametros ){
        $administradores = $this->getService()->obterComRestricoes( $parametros );

        return $this->resposta( HttpStatusCode::OK, (array) $administradores );
    }

    public function obterComId( array $corpoRequisicao, $args ){
        $id = intval( $args['id'] );

        $administrador = $this->getService()->obterComId( $id );
        if( ! $administrador instanceof Administrador ){
            throw new NaoEncontradoException( 'Administrador não encontrado.' );
        }

        return $this->resposta( HttpStatusCode::OK, [ $administrador ] );
    }

    public function excluirComId( array $corpoRequisicao, $args ){
        $id = intval( $args['id'] );

        $administrador = $this->getService()->obterComId( $id );
        if( ! $administrador instanceof Administrador ){
            throw new NaoEncontradoException( 'Administrador não encontrado.' );
        }

        $this->getService()->desativarComId( $id );

        return $this->resposta( HttpStatusCode::NO_CONTENT );
    }
}