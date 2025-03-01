<?php

namespace app\services;

use app\services\Service;
use app\classes\Categoria;
use app\classes\utils\Validador;
use app\databases\BancoDadosRelacional;

class CategoriaService extends Service {

    const TAMANHO_MINIMO_NOME = 1;
    const TAMANHO_MAXIMO_NOME = 100;
    const TAMANHO_MINIMO_DESCRICAO = 1;
    const TAMANHO_MAXIMO_DESCRICAO = 500;

    protected function validar( $categoria, array &$erro = [] ){
        $this->validarNome( $categoria, $erro );
        $this->validarDescricao( $categoria, $erro );
    }

    private function validarNome( Categoria $categoria, array &$erro ){
        $validacaoTamanhoNome = Validador::validarTamanhoTexto( $categoria->getNome(), self::TAMANHO_MINIMO_NOME, self::TAMANHO_MAXIMO_NOME );
        if( $validacaoTamanhoNome == 0 ){
            $erro['nome'] = 'Preencha o nome.';
        } else if( $validacaoTamanhoNome == -1 ){
            $erro['nome'] = 'O nome deve ter entre ' . self::TAMANHO_MINIMO_NOME . ' e ' . self::TAMANHO_MAXIMO_NOME . ' caracteres.';
        } else if( $categoria->getId() == BancoDadosRelacional::ID_INEXISTENTE && $this->getDao()->existe( 'nome', $categoria->getNome() ) ){
            $erro['nome'] = 'Categoria já cadastrada com esse nome.';
        }
    }

    private function validarDescricao( Categoria $categoria, array &$erro ){
        $validacaoTamanhoDescricao = Validador::validarTamanhoTexto( $categoria->getDescricao(), self::TAMANHO_MINIMO_DESCRICAO, self::TAMANHO_MAXIMO_DESCRICAO );
        if( $validacaoTamanhoDescricao == 0 ){
            $erro['descricao'] = 'Preencha a descrição.';
        } else if( $validacaoTamanhoDescricao == -1 ){
            $erro['descricao'] = 'A descrição deve ter entre ' . self::TAMANHO_MINIMO_DESCRICAO . ' e ' . self::TAMANHO_MAXIMO_DESCRICAO . ' caracteres.';
        }
    }
}