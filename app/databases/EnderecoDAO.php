<?php

namespace app\databases;

use app\classes\Endereco;
use app\databases\DAOEmBDR;
use app\classes\utils\ConversorDados;

class EnderecoDAO extends DAOEmBDR {
    protected function nomeTabela(){
        return 'endereco';
    }

    protected function adicionarNovo( $endereco, ?int $idRecursoPai = null ){
        $comando = "INSERT INTO {$this->nomeTabela()} ( id, logradouro, cidade, bairro, numero, cep, complemento ) VALUES ( :id, :logradouro, :cidade, :bairro, :numero, :cep, :complemento )";

        $parametros = $this->parametros( $endereco );
        $parametros['idCliente'] = $idRecursoPai;

        $this->getBancoDados()->executar( $comando, $parametros );
    }

    protected function atualizar( $endereco, ?int $idRecursoPai = null ){
        $comando = "UPDATE {$this->nomeTabela()} SET logradouro = :logradouro, cidade = :cidade, bairro = :bairro, numero = :numero, cep = :cep, complemento = :complemento WHERE id = :id";
        $this->getBancoDados()->executar( $comando, $this->parametros( $endereco ) );
    }

    protected function parametros( $endereco ){
        return ConversorDados::converterEmArray( $endereco );
    }

    protected function obterQuery( array $restricoes, array &$parametros ){
        $nomeTabela = $this->nomeTabela();

        $select = "SELECT * FROM {$nomeTabela}";
        $where = ' WHERE ativo = 1 ';
        $join = '';
        $orderBy = '';

        if( isset( $restricoes['idCliente'] ) ){
            $where .= " AND {$nomeTabela}.idCliente = :idCliente ";
            $parametros['idCliente'] = $restricoes['idCliente'];
        }

        $comando = $select . $join . $where . $orderBy;
        return $comando;
    }

    protected function transformarEmObjeto( array $linhas ){
        return ConversorDados::converterEmObjeto( Endereco::class, $linhas );
    }
}