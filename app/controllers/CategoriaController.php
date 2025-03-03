<?php

namespace app\controllers;

use app\classes\Categoria;
use app\controllers\Controller;

class CategoriaController extends Controller {

    protected function criar( array $corpoRequisicao ){
        $categoria = new Categoria();
        $camposSimples = [ 'id', 'nome', 'descricao' ];
        $this->povoarSimples( $categoria, $camposSimples, $corpoRequisicao );

        return $categoria;
    }
}