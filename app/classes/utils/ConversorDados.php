<?php

namespace app\classes\utils;

use DateTime;
use ReflectionClass;
use app\classes\Model;

class ConversorDados {

    public static function converterEmObjeto( string $nomeClasse, array $dados ){
        $classe = new $nomeClasse();
        if( ! $classe instanceof Model ){
            return;
        }

        $reflection = new ReflectionClass( $classe );
        $atributos = $reflection->getProperties();
        foreach( $atributos as $atributo ){
            $nomeAtributo = $atributo->getName();
            if( ! isset( $dados[ $nomeAtributo ] ) ){
                continue;
            }

            $tipoAtributo = $atributo->getType();
            if( $tipoAtributo ){
                $metodo = 'set' . ucfirst( $nomeAtributo );
                if( method_exists( $classe, $metodo ) ){
                    if( ! $tipoAtributo->isBuiltin() ){
                        $nomeTipo = $tipoAtributo->getName();
                        $objeto = new $nomeTipo();
                        if( $objeto instanceof DateTime ){
                            $classe->$metodo( new DateTime( $dados[$nomeAtributo] ) );
                        }
                    } else {
                        $classe->$metodo( $dados[$nomeAtributo] );
                    }
                }
            }
        }

        return $classe;
    }

    public static function converterEmArray( Model $classe ) {
        $array = [];

        $reflection = new ReflectionClass( $classe );
        $atributos = $reflection->getProperties();
        foreach( $atributos as $atributo ){
            $nomeAtributo = $atributo->getName();
            $tipoAtributo = $atributo->getType();
            if( $tipoAtributo ){
                $metodo = 'get' . ucfirst( $nomeAtributo );
                if( method_exists( $classe, $metodo ) ){
                    if( ! $tipoAtributo->isBuiltin() ){
                        $nomeTipo = $tipoAtributo->getName();
                        $objeto = new $nomeTipo();
                        if( $objeto instanceof Model ){
                            $array[ 'id' . ucfirst( $nomeAtributo ) ] = $classe->$metodo()->getId();
                        } elseif( $objeto instanceof DateTime ){
                            $array[ $nomeAtributo ] = $classe->$metodo( 'Y-m-d H:i:s' );
                        }
                    } else {
                        $array[ $nomeAtributo ] = $classe->$metodo();
                    }
                }
            }
        }

        return $array;
    }
}
