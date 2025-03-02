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
            'message' => 'Administrador cadastrado com sucesso.'
        ] );
    }

    public function login( array $corpoRequisicao ){
        [ 'email' => $email, 'senha' => $senha ] = $corpoRequisicao;

        /** @var AdministradorService */
        $administradorService = $this->getService();
        /** @var TokenJWT */
        $tokenJWT = $administradorService->autenticar( $email, $senha );

        return $this->resposta( HttpStatusCode::OK, [
            'message' => 'Administrador autenticado com sucesso.',
            'data' => [
                'Token' => $tokenJWT->codigo(),
                'Duração' => $tokenJWT->validadeTokenFormatada()
            ]
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
            'message' => 'Administrador atualizado com suceso.'
        ] );
    }

    public function adicionarPermissoes( array $corpoRequisicao, $args ){
        $id = intval( $args['id'] );

        /** @var AdministradorService */
        $administradorService = $this->getService();

        $administrador = $administradorService->obterComId( $id );
        if( ! $administrador instanceof Administrador ){
            throw new NaoEncontradoException( 'Administrador não encontrado.' );
        }

        $permissoes = $corpoRequisicao['permissoes'];
        $administradorService->salvarPermissoes( $administrador, $permissoes );

        return $this->resposta( HttpStatusCode::OK, [
            'message' => 'Permissões salvas com sucesso.'
        ] );
    }

    public function obterTodos( array $corpoRequisicao, $args, array $parametros ){
        $administradores = $this->getService()->obterComRestricoes( $parametros );

        return $this->resposta( HttpStatusCode::OK, [
            'message' => 'Administradores obtidos com sucesso.',
            'data' => [
                $administradores
            ]
        ] );
    }

    public function obterComId( array $corpoRequisicao, $args ){
        $id = intval( $args['id'] );

        $administrador = $this->getService()->obterComId( $id );
        if( ! $administrador instanceof Administrador ){
            throw new NaoEncontradoException( 'Administrador não encontrado.' );
        }

        return $this->resposta( HttpStatusCode::OK, [
            'message' => 'Administrador obtido com sucesso.',
            'data' => [
                $administrador
            ]
        ] );
    }

    public function excluirComId( array $corpoRequisicao, $args ){
        $id = intval( $args['id'] );

        $administrador = $this->getService()->obterComId( $id );
        if( ! $administrador instanceof Administrador ){
            throw new NaoEncontradoException( 'Administrador não encontrado.' );
        }

        $this->getService()->excluirComId( $id );

        return $this->resposta( HttpStatusCode::NO_CONTENT );
    }
}