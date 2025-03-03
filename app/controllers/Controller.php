<?php

namespace app\controllers;

use DateTime;
use Throwable;
use app\classes\http\HttpStatusCode;
use app\classes\Model;
use app\services\Service;

abstract class Controller {
    protected Service $service;

    public function __construct( Service $service ){
        $this->setService( $service );
    }

    protected function getService(){
        return $this->service;
    }

    protected function setService( Service $service ){
        $this->service = $service;
    }

    abstract protected function criar( array $corpoRequisicao );

    public function novo( array $dados ){
        $objeto = $this->criar( $dados );
        $this->getService()->salvar( $objeto );

        return $this->resposta( HttpStatusCode::CREATED, [
            'message' => 'Cadastrado com sucesso.'
        ] );
    }

    public function editar( array $dados, $args ){
        $id = intval( $args['id'] );
        $dados['id'] = $id;

        $objeto = $this->criar( $dados );
        $this->getService()->salvar( $objeto );

        return $this->resposta( HttpStatusCode::OK, [
            'message' => 'Atualizado com suceso.'
        ] );
    }

    public function obterTodos( array $dados, $args, array $parametros ){
        $objeto = $this->getService()->obterComRestricoes( $parametros );

        return $this->resposta( HttpStatusCode::OK, [
            'message' => 'Sucesso ao obter os dados.',
            'data' => [
                $objeto
            ]
        ] );
    }

    public function obterComId( array $dados, $args ){
        $id = intval( $args['id'] );
        $objetos = $this->getService()->obterComId( $id );

        return $this->resposta( HttpStatusCode::OK, [
            'message' => 'Sucesso ao obter os dados.',
            'data' => [
                $objetos
            ]
        ] );
    }

    public function excluirComId( array $dados, $args ){
        $id = intval( $args['id'] );
        $this->getService()->excluirComId( $id );

        return $this->resposta( HttpStatusCode::NO_CONTENT );
    }

    protected function povoarSimples( Model $objeto, array $campos, array $corpoRequisicao ){
        foreach( $campos as $campo ){
            if( isset( $corpoRequisicao[ $campo ] ) ){
                $metodo = 'set' . ucfirst( $campo );
                if( method_exists( $objeto, $metodo ) ){
                    try{
                        $objeto->$metodo( $corpoRequisicao[ $campo ] );
                    } catch( Throwable $e ){}
                }
            }
        }
    }

    protected function povoarDateTime( Model $objeto, array $campos, array $corpoRequisicao ){
        foreach( $campos as $campo ){
            if( isset( $corpoRequisicao[ $campo ] ) ){
                $metodo = 'set' . ucfirst( $campo );
                if( method_exists( $objeto, $metodo ) ){
                    $data = DateTime::createFromFormat( 'd/m/Y', $corpoRequisicao[ $campo ] );
                    if( $data ){
                        $objeto->$metodo( $data );
                    }
                }
            }
        }
    }

    protected function resposta( int $status = HttpStatusCode::OK, array $data = [] ){
        return [
            'status' => $status,
            'data' => $data
        ];
    }
}