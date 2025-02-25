<?php

namespace app\services;

use app\classes\Categoria;

class CategoriaService extends Service {

    protected function validar( $categoria, array &$erro = [] ){
        $this->validarNome( $categoria, $erro );
        $this->validarDescricao( $categoria, $erro );
    }

    private function validarNome( Categoria $categoria, array &$erro ){
        $this->validarTexto( $categoria->getNome(), Categoria::TAMANHO_MINIMO_NOME, Categoria::TAMANHO_MAXIMO_NOME, 'nome', $erro );
    }

    private function validarDescricao( Categoria $categoria, array &$erro ){
        $this->validarTexto( $categoria->getDescricao(), Categoria::TAMANHO_MINIMO_DESCRICAO, Categoria::TAMANHO_MAXIMO_DESCRICAO, 'descricao', $erro );
    }
}