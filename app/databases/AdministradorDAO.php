<?php

namespace app\databases;

use app\classes\Administrador;
use app\classes\utils\ConversorDados;

class AdministradorDAO extends DAOEmBDR {
    protected function nomeTabela(){
        return 'administrador';
    }

    protected function adicionarNovo( $administrador ){
        $comando = "INSERT INTO {$this->nomeTabela()} ( id, nome, email, senha ) VALUES ( :id, :nome, :email, :senha )";
        $this->getBancoDados()->executar( $comando, $this->parametros( $administrador ) );
    }

    protected function atualizar( $administrador ){
        $comando = "UPDATE {$this->nomeTabela()} SET nome = :nome, email = :email, senha = :senha WHERE id = :id";
        $this->getBancoDados()->executar( $comando, $this->parametros( $administrador ) );
    }

    protected function parametros( $administrador ){
        $parametros = ConversorDados::converterEmArray( $administrador );
        unset( $parametros['permissoes'] );

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

        $comando = $select . $join . $where . $orderBy;
        return $comando;
    }

    protected function transformarEmObjeto( array $linhas ){
        /** @var Administrador */
        $administrador = ConversorDados::converterEmObjeto( Administrador::class, $linhas );

        $permissoes = $this->permissoesDoAdministrador( $administrador ) ;
        $administrador->setPermissoes( $permissoes );

        return $administrador;
    }

    protected function permissoesDoAdministrador( Administrador $administrador ){
        $comando = "SELECT permissao.descricao FROM permissao_administrador
            JOIN permissao ON permissao.id = permissao_administrador.idPermissao
                WHERE idAdministrador = :idAdministrador
                AND permissao_administrador.ativo = :ativo";
        $parametros = [
            'idAdministrador' => $administrador->getId(),
            'ativo' => 1
        ];

        $permissoes = $this->getBancoDados()->consultar( $comando, $parametros );
        if( ! empty( $permissoes ) ){
            return array_map( function( $permissao ){
                return $permissao['descricao'];
            }, $permissoes );
        }

        return [];
    }

    /**
     * Método responsável por obter o id das permissões passadas por parâmetro.
     *
     * @param array $permissoes
     * @return array
     */
    public function obterIdsPermissao( array $permissoes ){
        $comando = "SELECT id FROM permissao WHERE descricao IN (" . implode(',', array_fill(0, count($permissoes), '?')) . ") AND ativo = 1";
        $ids = $this->getBancoDados()->consultar( $comando, $permissoes );

        if( ! empty( $ids ) ){
            return array_map( function( $id ){
                return $id['id'];
            }, $ids );
        }

        return [];
    }

    public function limparPermissoes( Administrador $administrador ){
        $comando = 'DELETE FROM permissao_administrador WHERE idAdministrador = :idAdministrador';
        $parametros = [
            'idAdministrador' => $administrador->getId()
        ];
        return $this->getBancoDados()->executar( $comando, $parametros );
    }

    public function salvarPermissoes( Administrador $administrador, array $idsPermissao ){
        $this->getBancoDados()->executarComTransacao( function() use( $administrador, $idsPermissao ){
            foreach( $idsPermissao as $idPermissao ){
                $this->adicionarPermissao( $administrador, $idPermissao );
            }
        } );
    }

    public function adicionarPermissao( Administrador $administrador, int $idPermissao ){
        $comando = 'INSERT INTO permissao_administrador ( idAdministrador, idPermissao ) VALUES( :idAdministrador, :idPermissao )';
        $parametros = [
            'idAdministrador' => $administrador->getId(),
            'idPermissao' => $idPermissao
        ];
        return $this->getBancoDados()->executar( $comando, $parametros );
    }
}

