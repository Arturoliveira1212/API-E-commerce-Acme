<?php

namespace app\controllers;

use app\classes\Categoria;
use app\controllers\Controller;
use app\classes\http\HttpStatusCode;
use app\exceptions\NaoEncontradoException;

class CategoriaController extends Controller {

    protected function criar( array $corpoRequisicao ){
        $categoria = new Categoria();
        $camposSimples = [ 'nome', 'descricao' ];
        $this->povoarSimples( $categoria, $camposSimples, $corpoRequisicao );

        return $categoria;
    }

    public function novo( array $corpoRequisicao ){
        $categoria = $this->criar( $corpoRequisicao );
        $this->getService()->salvar( $categoria );

        return $this->resposta( HttpStatusCode::CREATED, [
            'message' => "Categoria cadastrada com sucesso."
        ] );
    }

    public function editar( array $corpoRequisicao, $args ){
        $id = intval( $args['id'] );

        $categoria = $this->getService()->obterComId( $id );
        if( ! $categoria instanceof Categoria ){
            throw new NaoEncontradoException( 'Categoria não encontrada.' );
        }

        $categoria = $this->criar( $corpoRequisicao );
        $categoria->setId( $id );
        $this->getService()->salvar( $categoria );

        return $this->resposta( HttpStatusCode::OK, [
            'message' => 'Categoria atualizada com suceso.'
        ] );
    }

    public function obterTodos( array $corpoRequisicao, $args, array $parametros ){
        $categoriaes = $this->getService()->obterComRestricoes( $parametros );

        return $this->resposta( HttpStatusCode::OK, [
            'message' => 'Categoriaes obtidas com sucesso.',
            'data' => [
                $categoriaes
            ]
        ] );
    }

    public function obterComId( array $corpoRequisicao, $args ){
        $id = intval( $args['id'] );

        $categoria = $this->getService()->obterComId( $id );
        if( ! $categoria instanceof Categoria ){
            throw new NaoEncontradoException( 'Categoria não encontrada.' );
        }

        return $this->resposta( HttpStatusCode::OK, [
            'message' => 'Categoria obtida com sucesso.',
            'data' => [
                $categoria
            ]
        ] );
    }

    public function excluirComId( array $corpoRequisicao, $args ){
        $id = intval( $args['id'] );

        $categoria = $this->getService()->obterComId( $id );
        if( ! $categoria instanceof Categoria ){
            throw new NaoEncontradoException( 'Categoria não encontrada.' );
        }

        $this->getService()->desativarComId( $id );

        return $this->resposta( HttpStatusCode::NO_CONTENT );
    }
}