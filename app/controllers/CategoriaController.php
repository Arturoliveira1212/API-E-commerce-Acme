<?php

namespace app\controllers;

use app\classes\Categoria;
use app\core\HttpStatusCode;
use app\exceptions\NaoEncontradoException;

class CategoriaController extends Controller {
    protected function criar( array $corpoRequisicao ){
        return new Categoria();
    }

    public function novo( array $corpoRequisicao ){
        $categoria = $this->criar( $corpoRequisicao );
        $id = $this->getService()->salvar( $categoria );

        return $this->resposta( HttpStatusCode::CREATED, [
            "Id {$id} cadastrado com sucesso"
        ] );
    }

    public function editar( array $corpoRequisicao, $args ){
        $id = intval( $args['id'] );

        $categoria = $this->getService()->obterComId( $id );
        if( ! $categoria instanceof Categoria ){
            throw new NaoEncontradoException( 'Categoria não encontrada' );
        }

        $this->getService()->salvar( $categoria );

        return $this->resposta( HttpStatusCode::OK, [
            'Registro atualizado com suceso.'
        ] );
    }

    public function obterComId( array $corpoRequisicao, $args ){
        $id = intval( $args['id'] );

        $categoria = $this->getService()->obterComId( $id );
        if( ! $categoria instanceof Categoria ){
            throw new NaoEncontradoException( 'Categoria não encontrada' );
        }

        return $this->resposta( HttpStatusCode::OK, [ $categoria ] );
    }

    public function obterTodos( array $corpoRequisicao, $args, array $parametros ){
        $categorias = $this->getService()->obterComRestricoes( $parametros );

        return $this->resposta( HttpStatusCode::OK, (array) $categorias );
    }

    public function excluirComId( array $corpoRequisicao, $args ){
        $id = intval( $args['id'] );

        $categoria = $this->getService()->obterComId( $id );
        if( ! $categoria instanceof Categoria ){
            throw new NaoEncontradoException( 'Categoria não encontrada' );
        }

        $this->getService()->desativarComId( $id );

        return $this->resposta( HttpStatusCode::NO_CONTENT );
    }
}