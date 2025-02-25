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

    protected function validarTexto( string $texto, int $tamanhoMinimo, int $tamanhoMaximo, string $nomeAtributo, array &$erro ){
        $tamanhoTexto = mb_strlen( $texto );
        if( $tamanhoTexto == 0 ){
            $erro[ $nomeAtributo ] = "Preencha o campo {$nomeAtributo}.";
        } elseif( $tamanhoTexto > $tamanhoMaximo || $tamanhoTexto < $tamanhoMinimo ){
            $erro[ $nomeAtributo ] = "O campo {$nomeAtributo} deve ter entre {$tamanhoMinimo} e {$tamanhoMaximo} caracteres.";
        }
    }

    public function salvar( Model $objeto ){
        $erro = [];
        $this->validar( $objeto, $erro );
        if( ! empty( $erro ) ){
            throw new ServiceException( json_encode( $erro ) );
        }

        return $this->getDao()->salvar( $objeto );
    }

    public function desativarComId( int $id ){
        return $this->getDao()->desativarComId( $id );
    }

    public function existe( string $campo, string $valor ){
        return $this->getDao()->existe( $campo, $valor );
    }

    public function obterComId( int $id ){
        return $this->getDao()->obterComId( $id );
    }

    public function obterComRestricoes( array $restricoes ){
        return $this->getDao()->obterComRestricoes( $restricoes );
    }
}