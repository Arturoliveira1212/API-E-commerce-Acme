<?php

namespace app\controllers;

use app\classes\enum\OperacaoEstoque;
use app\controllers\Controller;
use app\classes\http\HttpStatusCode;
use app\classes\Item;
use app\services\ItemService;

class ItemController extends Controller {
    protected function criar( array $dados ){
        $item = new Item();
        $camposSimples = [ 'id', 'tamanho', 'estoque', 'pesoEmGramas' ];
        $this->povoarSimples( $item, $camposSimples, $dados );

        return $item;
    }

    public function movimentarEstoque( array $dados, $args ){
        $idItem = isset( $args['id'] ) ? intval( $args['id'] ) : 0;
        $quantidade = isset( $dados['quantidade'] ) ? intval( $dados['quantidade'] ) : 0;
        $operacaoEstoque = OperacaoEstoque::toEnum( $dados['operacao'] ?? '' ) ?? 0;

        /** @var ItemService */
        $itemService = $this->getService();
        $itemService->movimentarEstoque( $idItem, $quantidade, $operacaoEstoque );

        return $this->resposta( HttpStatusCode::OK, [
            'message' => 'Estoque atualizado com sucesso.'
        ] );
    }

    public function obterItensDoProduto( array $dados, $args ){
        $idProduto = isset( $args['id'] ) ? intval( $args['id'] ) : null;

        /** @var ItemService */
        $itemService = $this->getService();
        $itens = $itemService->obterItensDoProduto( $idProduto );

        return $this->resposta( HttpStatusCode::OK, [
            'message' => 'Sucesso ao obter os dados.',
            'data' => [
                $itens
            ]
        ] );
    }
}