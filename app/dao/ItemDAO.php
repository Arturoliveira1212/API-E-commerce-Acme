<?php

namespace app\dao;

use app\classes\Item;
use app\dao\DAOEmBDR;
use app\classes\enum\OperacaoEstoque;
use app\classes\enum\OperacaoObjeto;
use app\classes\utils\ConversorDados;

class ItemDAO extends DAOEmBDR {
    protected function nomeTabela(){
        return 'item';
    }

    public function salvar( $item, int $operacaoObjeto, ?int $idRecursoPai = null ){
        if( $operacaoObjeto == OperacaoObjeto::CADASTRAR && $item->getEstoque() > 0 ){
            $this->salvarItemComEstoque( $item, $idRecursoPai );
        } else if( $operacaoObjeto == OperacaoObjeto::CADASTRAR ){
            $this->adicionarNovo( $item, $idRecursoPai );
        } else if( $operacaoObjeto == OperacaoObjeto::EDITAR ){
            $this->atualizar( $item );
        }

        return $this->getBancoDados()->ultimoIdInserido();
    }

    private function salvarItemComEstoque( Item $item, ?int $idRecursoPai = null ){
        $this->getBancoDados()->executarComTransacao( function() use( $item, $idRecursoPai ){
            $this->adicionarNovo( $item, $idRecursoPai );
            $item->setId( $this->getBancoDados()->ultimoIdInserido() );

            $this->atualizarEstoque( $item, $item->getEstoque(), OperacaoEstoque::ADICIONAR );
        } );
    }

    protected function adicionarNovo( $item, ?int $idRecursoPai = null ){
        $comando = "INSERT INTO {$this->nomeTabela()} ( id, idProduto, sku, tamanho, estoque, pesoEmGramas ) VALUES ( :id, :idProduto, :sku, :tamanho, :estoque, :pesoEmGramas )";

        $parametros = $this->parametros( $item );
        $parametros['idProduto'] = $idRecursoPai;

        $this->getBancoDados()->executar( $comando, $parametros );
    }

    public function atualizarEstoque( Item $item, int $quantidade, int $operacaoEstoque ){
        $sinal = $operacaoEstoque == OperacaoEstoque::ADICIONAR ? '+' : '-';
        $comando = "UPDATE item SET estoque = estoque $sinal :quantidade WHERE id = :id";
        $parametros = [
            'id' => $item->getId(),
            'quantidade' => $quantidade
        ];

        return $this->getBancoDados()->executar( $comando, $parametros );
    }

    // TO DO => Criar DAO própria para MovimentacaoEstoque
    public function registrarMovimentacaoEstoque( Item $item, int $idAdministrador, int $quantidade, int $operacaoEstoque ){
        $comando = 'INSERT INTO movimentacao_estoque_item ( idItem, idAdministrador, operacao, quantidade, data ) VALUES( :idItem, :idAdministrador, :operacao, :quantidade, NOW() )';
        $parametros = [
            'idItem' => $item->getId(),
            'operacao' => $operacaoEstoque,
            'quantidade' => $quantidade,
            'idAdministrador' => $idAdministrador
        ];

        return $this->getBancoDados()->executar( $comando, $parametros );
    }

    protected function atualizar( $item, ?int $idRecursoPai = null ){
        $comando = "UPDATE {$this->nomeTabela()} SET sku = :sku, tamanho = :tamanho, pesoEmGramas = :pesoEmGramas WHERE id = :id";

        $parametros = $this->parametros( $item );
        unset( $parametros['estoque'] );

        $this->getBancoDados()->executar( $comando, $parametros );
    }

    protected function parametros( $item ){
        return ConversorDados::converterEmArray( $item );
    }

    protected function obterQuery( array $restricoes, array &$parametros ){
        $nomeTabela = $this->nomeTabela();

        $select = "SELECT * FROM {$nomeTabela}";
        $where = ' WHERE ativo = 1 ';
        $join = '';
        $orderBy = '';

        if( isset( $restricoes['sku'] ) ){
            $where .= " AND {$nomeTabela}.sku = :sku ";
            $parametros['sku'] = $restricoes['sku'];
        }

        if( isset( $restricoes['idProduto'] ) ){
            $where .= " AND {$nomeTabela}.idProduto = :idProduto ";
            $parametros['idProduto'] = $restricoes['idProduto'];
        }

        $comando = $select . $join . $where . $orderBy;
        return $comando;
    }

    protected function transformarEmObjeto( array $linhas ){
        return ConversorDados::converterEmObjeto( Item::class, $linhas );
    }
}