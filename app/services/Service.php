<?php

namespace app\services;

use app\classes\Model;
use app\databases\DAO;
use app\exceptions\ServiceException;

abstract class Service {
    protected DAO $dao;

    public function __construct( DAO $dao ){
        $this->setDao( $dao );
    }

    protected function getDao(){
        return $this->dao;
    }

    protected function setDao( DAO $dao ){
        $this->dao = $dao;
    }

    abstract protected function validar( Model $objeto, array &$erro = [] );

    protected function preSalvar( Model $objeto ){
        $erro = [];
        $this->validar( $objeto, $erro );
        if( ! empty( $erro ) ){
            throw new ServiceException( json_encode( $erro ) );
        }
    }

    public function salvar( Model $objeto ){
        $this->preSalvar( $objeto );
        return $this->getDao()->salvar( $objeto );
    }

    public function desativarComId( int $id ){
        return $this->getDao()->desativarComId( $id );
    }

    public function excluirComId( int $id ){
        return $this->getDao()->excluirComId( $id );
    }

    public function existe( string $campo, string $valor ){
        return $this->getDao()->existe( $campo, $valor );
    }

    public function obterComId( int $id ){
        return $this->getDao()->obterComId( $id );
    }

    public function camposOrdenaveis(){
        // TO DO => Implementar orderBy
        return [ 'id '];
    }

    public function obterComRestricoes( array $restricoes ){
        $this->filtrarRestricoes( $restricoes );
        return $this->getDao()->obterComRestricoes( $restricoes );
    }

    private function filtrarRestricoes( array &$restricoes ){
        if( isset( $restricoes['limit'] ) && ! empty( $restricoes['limit'] ) && ! is_numeric( $restricoes['limit'] ) ){
            unset( $restricoes['limit'] );
        }

        if( isset( $restricoes['offset'] ) && ! empty( $restricoes['offset'] ) && ! is_numeric( $restricoes['offset'] ) ){
            unset( $restricoes['offset'] );
        }
    }
}