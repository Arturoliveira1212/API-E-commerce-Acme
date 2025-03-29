<?php

namespace app\classes\enum;

class OperacaoObjeto implements Enum {
    const CADASTRAR = 1;
	const EDITAR = 2;

    public static function ehValido( $enum ){
        return array_key_exists( $enum, self::toArray() );
    }

    public static function quantidadeOpcoes(){
        return count( self::toArray() );
    }

    public static function toArray(){
        return [
			self::CADASTRAR => 'CADASTRAR',
			self::EDITAR => 'EDITAR',
        ];
    }

    public static function toString( $operacaoEstoque ){
        return self::toArray()[$operacaoEstoque];
    }

    public static function toEnum( $conteudo ){
        foreach( self::toArray() as $operacao => $conteudoOperacao ){
            if( $conteudoOperacao == $conteudo ){
                return $operacao;
            }
        }
    }
}