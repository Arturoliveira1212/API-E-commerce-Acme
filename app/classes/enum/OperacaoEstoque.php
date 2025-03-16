<?php

namespace app\classes\enum;

class OperacaoEstoque extends Enum {
    const ADICIONAR = 1;
	const REMOVER = 2;

    public static function toArray(){
        return [
			self::ADICIONAR => 'Estoque adicionado',
			self::REMOVER => 'Esoque removido',
        ];
    }

    public static function toString( $operacaoEstoque ){
        return self::toArray()[$operacaoEstoque];
    }

    public static function toEnum( $conteudo) {
        return (int) array_search( $conteudo, self::toArray() );
    }
}