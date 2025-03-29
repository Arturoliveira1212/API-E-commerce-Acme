<?php

namespace app\services;

use app\services\Service;
use app\classes\Categoria;
use app\classes\enum\OperacaoObjeto;
use app\classes\utils\Validador;

class CategoriaService extends Service {
    const TAMANHO_MINIMO_NOME = 2;
    const TAMANHO_MAXIMO_NOME = 100;
    const TAMANHO_MINIMO_DESCRICAO = 5;
    const TAMANHO_MAXIMO_DESCRICAO = 500;

    protected function validar( $categoria, int $operacaoObjeto, array &$erro = [] ){
        $this->validarNome( $categoria, $operacaoObjeto, $erro );
        $this->validarDescricao( $categoria, $erro );
    }

    // TO DO => Verificar método, validação de existe está errada.
    private function validarNome( Categoria $categoria, int $operacaoObjeto, array &$erro ){
        $validacaoTamanhoNome = Validador::validarTamanhoTexto( $categoria->getNome(), self::TAMANHO_MINIMO_NOME, self::TAMANHO_MAXIMO_NOME );
        if( $validacaoTamanhoNome == 0 ){
            $erro['nome'] = 'Preencha o nome.';
        } else if( $validacaoTamanhoNome == -1 ){
            $erro['nome'] = 'O nome deve ter entre ' . self::TAMANHO_MINIMO_NOME . ' e ' . self::TAMANHO_MAXIMO_NOME . ' caracteres.';
        } else if( $operacaoObjeto == OperacaoObjeto::CADASTRAR && $this->getDao()->existe( 'nome', $categoria->getNome() ) ){
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