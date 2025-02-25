<?php

namespace app\databases;

use app\classes\Model;

abstract class DAOEmBDR implements DAO {
    private ?BancoDadosRelacional $bancoDados = null;

    public function __construct( BancoDadosRelacional $bancoDados ){
        $this->bancoDados = $bancoDados;
    }

    protected function getBancoDados(){
        return $this->bancoDados;
    }

    abstract protected function nomeTabela();
    abstract protected function adicionarNovo( Model $objeto );
    abstract protected function atualizar( Model $objeto );
    abstract protected function parametros( Model $objeto );
    abstract protected function obterQuery( array $restricoes, array &$parametros );
    abstract protected function transformarEmObjeto( array $linhas );

    public function salvar( $objeto ){
        $salvar = function() use ( $objeto ) {
            if( $objeto->getId() == BancoDadosRelacional::ID_INEXISTENTE ){
                $this->adicionarNovo( $objeto );
            } else {
                $this->atualizar( $objeto );
            }

            return $this->getBancoDados()->ultimoIdInserido();
        };

        return $this->getBancoDados()->executarComTransacao( $salvar );
    }

    public function desativarComId( int $id ){
        $desativarComId = function() use ( $id ){
            return $this->getBancoDados()->desativar( $this->nomeTabela(), $id );
        };

        return $this->getBancoDados()->executarComTransacao( $desativarComId );
    }

    public function existe( string $campo, string $valor ){
        return $this->getBancoDados()->existe( $this->nomeTabela(), $campo, $valor );
    }

    public function obterComId( int $id ){
        $comando = "SELECT * FROM {$this->nomeTabela()} WHERE id = :id AND ativo = :ativo";
        $parametros = [ 'id' => $id, 'ativo' => true ];
        $objetos = $this->obterObjetos( $comando, [ $this, 'transformarEmObjeto' ], $parametros );
        return ! empty( $objetos ) ? array_shift( $objetos ) : null;
    }

    public function obterComRestricoes( array $restricoes ){
        $parametros = [];
        $query = $this->obterQuery( $restricoes, $parametros );
        return $this->obterObjetos( $query, [ $this, 'transformarEmObjeto' ], $parametros );
    }

    public function obterObjetos( string $comando, array $callback, array $parametros = [] ){
        $objetos = [];

        $resultados = $this->getBancoDados()->consultar( $comando, $parametros );

        if( ! empty( $resultados ) ){
            foreach( $resultados as $resultado ){
                $objeto = call_user_func_array( $callback, [ $resultado ] );
                $objetos[] = $objeto;
            }
        }

        return $objetos;
    }
}