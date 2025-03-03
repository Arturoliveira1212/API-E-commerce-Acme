<?php

namespace app\classes\factory;

use app\classes\Administrador;
use app\classes\Cliente;
use app\classes\factory\ClassFactory;
use app\middlewares\PermissaoMiddleware;
use app\middlewares\AutenticacaoMiddleware;
use app\middlewares\CorpoRequisicaoMiddleware;

class MiddlewareFactory {
    public static function permissao( array $papeis, array $permissoes = [] ){
        return new PermissaoMiddleware(
            $papeis,
            ClassFactory::makeService( Administrador::class ),
            ClassFactory::makeService( Cliente::class ),
            $permissoes
        );
    }

    public static function autenticacao(){
        return new AutenticacaoMiddleware();
    }

    public static function corpoRequisicao( array $schema, string $contentType = 'application/json' ){
        return new CorpoRequisicaoMiddleware( $contentType, $schema );
    }
}
