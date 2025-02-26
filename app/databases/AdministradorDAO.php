<?php

namespace app\databases;

use app\classes\Administrador;
use app\classes\utils\ConversorDados;

class AdministradorDAO extends DAOEmBDR {
    protected function nomeTabela(){
        return 'administrador';
    }

    protected function adicionarNovo( $categoria ){
        $comando = "INSERT INTO {$this->nomeTabela()} ( id, nome, email, senha ) VALUES ( :id, :nome, :email, :senha )";
        $this->getBancoDados()->executar( $comando, $this->parametros( $categoria ) );
    }

    protected function atualizar( $categoria ){
        $comando = "UPDATE {$this->nomeTabela()} SET nome = :nome, email = :email, senha = :senha WHERE id = :id";
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
        $limit = '';
        $offset = '';

        if( isset( $restricoes['email'] ) ){
            $where .= " AND {$nomeTabela}.email = :email ";
            $parametros['email'] = $restricoes['email'];
        }

        if( isset( $restricoes['limit'] ) && is_numeric( $restricoes['limit'] ) ){
            $limit = " LIMIT {$restricoes['limit']} ";

            if( isset( $restricoes['offset'] ) && is_numeric( $restricoes['offset'] ) ){
                $offset = " OFFSET {$restricoes['offset']} ";
            }
        }

        $comando = $select . $join . $where . $orderBy . $limit . $offset;
        return $comando;
    }

    protected function transformarEmObjeto( array $linhas ){
        return ConversorDados::converterEmObjeto( Administrador::class, $linhas );
    }
}

