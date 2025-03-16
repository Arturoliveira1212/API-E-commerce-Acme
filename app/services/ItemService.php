<?php

namespace app\services;

use app\dao\ItemDAO;
use app\classes\Produto;
use app\services\Service;
use app\dao\BancoDadosRelacional;
use app\exceptions\ServiceException;
use app\classes\factory\ClassFactory;
use app\classes\Item;
use app\exceptions\NaoEncontradoException;

class ItemService extends Service {
    const TAMANHOS_DISPONIVEIS = [
        'PP', 'P', 'M', 'G', 'GG', 'XG'
    ];

    protected function preSalvar( $item, ?int $idRecursoPai = null ){
        if( $item->getId() == BancoDadosRelacional::ID_INEXISTENTE && ! $this->produtoDoItemExiste( $idRecursoPai ) ){
            throw new NaoEncontradoException( 'Recurso não encontrado.' );
        }

        parent::preSalvar( $item );
    }

    private function produtoDoItemExiste( ?int $idProduto = null ){
        $produtoService = ClassFactory::makeService( Produto::class );
        $existe = $produtoService->existe( 'id', $idProduto );

        return $existe;
    }

    protected function validar( $item, array &$erro = [] ){
        $this->validarTamanho( $item, $erro );
        $this->validarEstoque( $item, $erro );
    }

    private function validarTamanho( Item $item, array &$erro ){
        if( empty( $item->getTamanho() ) ){
            $erro['tamanho'] = 'Preencha o tamanho.';
        } else if( ! in_array( $item->getTamanho(), self::TAMANHOS_DISPONIVEIS ) ){
            $tamanhosDisponiveis = implode( ',', self::TAMANHOS_DISPONIVEIS );
            $erro['tamanho'] = "Tamanho inválido. Os tamanhos disponíveis são: {$tamanhosDisponiveis}";
        }
    }

    private function validarEstoque( Item $item, array &$erro ){
        if( $item->getId() == BancoDadosRelacional::ID_INEXISTENTE ){
            
        }
    }

    public function movimentarEstoque( int $idItem, int $quantidade, int $operacaoEstoque ){
        $item = $this->obterComId( $idItem );

        $erro = [];
        $this->validarMovimentacaoEstoque( $quantidade, $operacaoEstoque, $erro );
        if( ! empty( $erro ) ){
            throw new ServiceException( json_encode( $erro ) );
        }

        /** @var ItemDAO */
        $itemDAO = $this->getDao();
        $itemDAO->movimentarEstoque( $item, $quantidade, $operacaoEstoque );
    }

    private function validarMovimentacaoEstoque( int $quantidade, int $operacaoEstoque, array &$erro = [] ){
        $this->validarQuantidade( $quantidade, $erro );
        $this->validarOperacaoEstoque( $operacaoEstoque, $erro );
    }

    public function obterItensDoProduto( int $idProduto ){
        if( ! $this->produtoDoItemExiste( $idProduto ) ){
            throw new NaoEncontradoException( 'Recurso não encontrado.' );
        }

        $restricoes = [ 'idProduto' => $idProduto ];
        $itens = $this->obterComRestricoes( $restricoes );

        return $itens;
    }
}