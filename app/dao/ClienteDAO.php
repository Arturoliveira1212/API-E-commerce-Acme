<?php

namespace app\dao;

use app\classes\Cliente;
use app\classes\Endereco;
use app\classes\factory\ClassFactory;
use app\classes\utils\ConversorDados;
use app\services\EnderecoService;

class ClienteDAO extends DAOEmBDR {
    protected function nomeTabela(){
        return 'cliente';
    }

    protected function adicionarNovo( $cliente, ?int $idRecursoPai = null ){
        $comando = "INSERT INTO {$this->nomeTabela()} ( id, nome, email, cpf, senha, dataNascimento ) VALUES ( :id, :nome, :email, :cpf, :senha, :dataNascimento )";
        $this->getBancoDados()->executar( $comando, $this->parametros( $cliente ) );
    }

    protected function atualizar( $cliente ){
        $comando = "UPDATE {$this->nomeTabela()} SET nome = :nome, email = :email, cpf = :cpf, senha = :senha, dataNascimento = :dataNascimento WHERE id = :id";
        $this->getBancoDados()->executar( $comando, $this->parametros( $cliente ) );
    }

    protected function parametros( $cliente ){
        $parametros = ConversorDados::converterEmArray( $cliente );
        unset( $parametros['enderecos'] );

        return $parametros;
    }

    protected function obterQuery( array $restricoes, array &$parametros ){
        $nomeTabela = $this->nomeTabela();

        $select = "SELECT * FROM {$nomeTabela}";
        $where = ' WHERE ativo = 1 ';
        $join = '';
        $orderBy = '';

        if( isset( $restricoes['email'] ) ){
            $where .= " AND {$nomeTabela}.email = :email ";
            $parametros['email'] = $restricoes['email'];
        }

        if( isset( $restricoes['cpf'] ) ){
            $where .= " AND {$nomeTabela}.cpf = :cpf ";
            $parametros['cpf'] = $restricoes['cpf'];
        }

        $comando = $select . $join . $where . $orderBy;
        return $comando;
    }

    protected function transformarEmObjeto( array $linhas ){
        /** @var Cliente */
        $cliente = ConversorDados::converterEmObjeto( Cliente::class, $linhas );
        $this->preencherEnderecosDoCliente( $cliente );

        return $cliente;
    }

    private function preencherEnderecosDoCliente( Cliente $cliente ){
        /** @var EnderecoService */
        $enderecoService = ClassFactory::makeService( Endereco::class );
        $enderecosDoCliente = $enderecoService->obterEnderecosDoCliente( $cliente->getId() );
        if( ! empty( $enderecosDoCliente ) ){
            $cliente->setEnderecos( $enderecosDoCliente );
        }
    }
}