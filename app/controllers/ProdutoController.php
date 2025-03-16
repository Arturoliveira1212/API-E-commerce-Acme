<?php

namespace app\controllers;

use app\classes\Produto;
use app\classes\Categoria;
use app\controllers\Controller;
use app\services\CategoriaService;
use app\classes\http\HttpStatusCode;
use app\classes\factory\ClassFactory;
use app\exceptions\NaoEncontradoException;
use app\services\ProdutoService;

class ProdutoController extends Controller {
    protected function criar( array $dados ){
        $produto = new Produto();
        $camposSimples = [ 'id', 'nome', 'referencia', 'cor', 'preco', 'descricao' ];
        $this->povoarSimples( $produto, $camposSimples, $dados );

        if( isset( $dados['categoria'] ) ){
            // TO DO => Criar povoarObjetos genérico.
            $this->povoarCategoria( $produto, intval( $dados['categoria'] ) );
        }

        return $produto;
    }

    private function povoarCategoria( Produto $produto, int $idCategoria ){
        try {
            /** @var CategoriaService */
            $categoriaService = ClassFactory::makeService( Categoria::class );
            $categoria = $categoriaService->obterComId( $idCategoria );
        } catch( NaoEncontradoException $e ){
            $categoria = null;
        }

        $produto->setCategoria( $categoria );
    }

    public function obterComReferencia( array $dados, $args ){
        $referencia = $args['referencia'] ?? null;

        /** @var ProdutoService */
        $produtoService = $this->getService();
        $produto = $produtoService->obterComReferencia( $referencia );

        return $this->resposta( HttpStatusCode::OK, [
            'message' => 'Sucesso ao obter os dados.',
            'data' => [
                $produto
            ]
        ] );
    }
}