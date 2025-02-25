<?php

namespace app\databases;

class CategoriaDAO extends DAOEmBDR {

    protected function nomeTabela(){
        return 'categoria';
    }

    protected function adicionarNovo( $categoria ){
        $comando = "INSERT INTO {$this->nomeTabela()} ( id, nome, descricao ) VALUES ( :id, :nome, :descricao )";
        $this->getBancoDados()->executar( $comando, $this->parametros( $categoria ) );
    }

    protected function atualizar( $categoria ){
        $comando = "UPDATE {$this->nomeTabela()} SET nome = :nome, descricao = :descricao WHERE id = :id";
        $this->getBancoDados()->executar( $comando, $this->parametros( $categoria ) );
    }

    protected function parametros( $categoria ){
        return [];
    }

    protected function obterQuery( array $restricoes, array &$parametros ){
        $nomeTabela = $this->nomeTabela();

        $select = "SELECT * FROM {$nomeTabela}";
        $where = ' WHERE ativo = 1 ';
        $join = '';
        $orderBy = '';
        $limit = '';
        $offset = '';

        // $restricoes = $queryParams->getRestricoes();
        // if( ! empty( $restricoes ) ){
        //     if( isset( $restricoes['id'] ) ){
        //         $where .= " AND $nomeTabela.id = :id";
        //         $parametros['id'] = $restricoes['id'];
        //     }
        // }

        // $orderByQuery = $queryParams->getOrderBy();
        // if( ! empty( $orderByQuery ) ){
        //     $orderBy = " ORDER BY {$orderByQuery}";
        // }

        // $limitQuery = $queryParams->getLimit();
        // if( ! empty( $limitQuery ) && is_numeric( $limitQuery ) ){
        //     $limit = " LIMIT {$limitQuery} ";

        //     $offsetQuery = $queryParams->getOffset();
        //     if( ! empty( $offsetQuery ) && is_numeric( $offsetQuery ) ){
        //         $offset = " OFFSET {$offsetQuery} ";
        //     }
        // }

        $comando = $select . $join . $where . $orderBy . $limit . $offset;
        return $comando;
    }

    protected function transformarEmObjeto( array $linhas ){
        // return $this->converterEmObjeto( Categoria::class, $linhas );
    }
}