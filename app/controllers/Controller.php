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

    public function getService(){
        return $this->service;
    }

    public function setService( Service $service ){
        $this->service = $service;
    }

    abstract protected function criar( array $corpoRequisicao );

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
            'status' => HttpStatusCode::CREATED,
            'data' => $data
        ];
    }
}