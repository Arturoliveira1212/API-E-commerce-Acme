<?php

namespace app\classes\enum;

abstract class Enum {
    public static function ehValido( $enum ){
        return array_key_exists( $enum, self::toArray() );
    }

    public static function quantidadeOpcoes(){
        return count( self::toArray() );
    }

	abstract public static function toString( $enum );
	abstract public static function toEnum( $conteudo );
	abstract public static function toArray();
}