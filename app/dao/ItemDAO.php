<?php

namespace app\dao;

use app\classes\Item;
use app\dao\DAOEmBDR;
use app\dao\BancoDadosRelacional;
use app\classes\enum\OperacaoEstoque;
use app\classes\utils\ConversorDados;

class ItemDAO extends DAOEmBDR {
    protected function nomeTabela(){
        return 'item';
    }

    public function salvar( $objeto, ?int $idRecursoPai = null ){
        if( $objeto->getId() == BancoDadosRelacional::ID_INEXISTENTE ){
            $this->salvarItem( $objeto, $idRecursoPai );
        } else {
            $this->atualizar( $objeto );
        }

        return $this->getBancoDados()->ultimoIdInserido();
    }

    private function salvarItem( Item $item, ?int $idRecursoPai = null ){
        $this->getBancoDados()->executarComTransacao( function() use( $item, $idRecursoPai ){
            $this->adicionarNovo( $item, $idRecursoPai );
            $this->atualizarEstoque( $item, $item->getEstoque(), OperacaoEstoque::ADICIONAR );
            $this->registrarMovimentacaoEstoque( $item, $item->getEstoque(), OperacaoEstoque::ADICIONAR );
        } );
    }

    protected function adicionarNovo( $item, ?int $idRecursoPai = null ){
        $comando = "INSERT INTO {$this->nomeTabela()} ( id, idProduto, tamanho, estoque, pesoEmGramas ) VALUES ( :id, :idProduto, :tamanho, :estoque, :pesoEmGramas )";

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

    public function registrarMovimentacaoEstoque( Item $item, int $quantidade, int $operacaoEstoque ){
        $comando = 'INSERT INTO movimentacao_estoque_item ( id, idItem, operacao, quantidade ) VALUES( id:, :idItem, :operacao, :quantidade )';
        $parametros = [
            'idItem' => $item->getId(),
            'operacao' => $operacaoEstoque,
            'quantidade' => $quantidade
        ];

        return $this->getBancoDados()->executar( $comando, $parametros );
    }

    protected function atualizar( $item, ?int $idRecursoPai = null ){
        $comando = "UPDATE {$this->nomeTabela()} SET tamanho = :tamanho, pesoEmGramas = :pesoEmGramas WHERE id = :id";
        $this->getBancoDados()->executar( $comando, $this->parametros( $item ) );
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

    public function movimentarEstoque( Item $item, int $quantidade, int $operacaoEstoque ){
        $this->getBancoDados()->executarComTransacao( function() use ( $item, $quantidade, $operacaoEstoque ){
            $this->atualizarEstoque( $item, $quantidade, $operacaoEstoque );
            $this->registrarMovimentacaoEstoque( $item, $quantidade, $operacaoEstoque );
        } );
    }
}