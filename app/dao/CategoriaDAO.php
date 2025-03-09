<?php

namespace app\dao;

use app\classes\Categoria;
use app\classes\utils\ConversorDados;

class CategoriaDAO extends DAOEmBDR {
    protected function nomeTabela(){
        return 'categoria';
    }

    protected function adicionarNovo( $categoria, ?int $idRecursoPai = null ){
        $comando = "INSERT INTO {$this->nomeTabela()} ( id, nome, descricao ) VALUES ( :id, :nome, :descricao )";
        $this->getBancoDados()->executar( $comando, $this->parametros( $categoria ) );
    }

    protected function atualizar( $categoria ){
        $comando = "UPDATE {$this->nomeTabela()} SET nome = :nome, descricao = :descricao WHERE id = :id";
        $this->getBancoDados()->executar( $comando, $this->parametros( $categoria ) );
    }

    protected function parametros( $categoria ){
        return ConversorDados::converterEmArray( $categoria );
    }

    protected function obterQuery( array $restricoes, array &$parametros ){
        $nomeTabela = $this->nomeTabela();

        $select = "SELECT * FROM {$nomeTabela}";
        $where = ' WHERE ativo = 1 ';
        $join = '';
        $orderBy = '';

        if( isset( $restricoes['nome'] ) ){
            $where .= " AND {$nomeTabela}.nome = :nome ";
            $parametros['nome'] = $restricoes['nome'];
        }

        $comando = $select . $join . $where . $orderBy;
        return $comando;
    }

    protected function transformarEmObjeto( array $linhas ){
        return ConversorDados::converterEmObjeto( Categoria::class, $linhas );
    }
}