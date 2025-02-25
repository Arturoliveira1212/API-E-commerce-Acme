<?php

namespace app\core;

use app\databases\BancoDadosRelacional;
use InvalidArgumentException;

abstract class ClassFactory {
    const CAMINHO_CONTROLLER = 'app\\controllers\\';
    const CAMINHO_SERVICE = 'app\\services\\';
    const CAMINHO_DAO = 'app\\databases\\';

    /**
     * Método responsável por fabricar intâncias de controllers.
     *
     * @param string $nome
     * @throws InvalidArgumentException
     * @return Controller
     */
    public static function makeController( string $classe ){
        $controller = self::CAMINHO_CONTROLLER . $classe . 'Controller';
        if( ! class_exists( $controller ) ){
            throw new InvalidArgumentException( "Controller $controller não encontrado." );
        }

        return new $controller( self::makeService( $classe ) );
    }

    /**
     * Método responsável por fabricar intâncias de services.
     *
     * @param string $service
     * @throws InvalidArgumentException
     * @return Service
     */
    public static function makeService( string $classe ){
        $service = self::CAMINHO_SERVICE . $classe . 'Service';
        if( ! class_exists( $service ) ){
            throw new InvalidArgumentException( "Service $service não encontrado." );
        }

        return new $service( self::makeDAO( $classe ) );
    }

    /**
     * Método responsável por fabricar intâncias de DAOs.
     *
     * @param string $nomeDAO
     * @throws InvalidArgumentException
     * @return DAO
     */
    public static function makeDAO( string $classe ){
        $DAO = self::CAMINHO_DAO . $classe . 'DAO';
        if( ! class_exists( $DAO ) ){
            throw new InvalidArgumentException( "DAO $classe não encontrado." );
        }

        return new $DAO( new BancoDadosRelacional() );
    }
}