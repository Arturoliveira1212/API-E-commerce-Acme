<?php

namespace app\classes\factory;

use app\classes\Cliente;
use app\classes\Endereco;
use app\classes\Administrador;
use app\classes\factory\ClassFactory;
use app\middlewares\PermissaoMiddleware;
use app\middlewares\AutenticacaoMiddleware;
use app\middlewares\CorpoRequisicaoMiddleware;
use app\middlewares\PermissaoClienteMiddleware;
use app\middlewares\PermissaoEnderecoMiddleware;
use app\middlewares\PermissaoAdministradorMiddleware;

class MiddlewareFactory {
    public static function permissao( ...$tiposPermissao ){
        return new PermissaoMiddleware( $tiposPermissao );
    }

    public static function permissaoAdministrador( array $permissoesNecessarias ){
        return new PermissaoAdministradorMiddleware( $permissoesNecessarias, ClassFactory::makeService( Administrador::class ) );
    }

    public static function permissaoCliente(){
        return new PermissaoClienteMiddleware( ClassFactory::makeService( Cliente::class ) );
    }

    public static function permissaoEndereco(){
        return new PermissaoEnderecoMiddleware( ClassFactory::makeService( Cliente::class ), ClassFactory::makeService( Endereco::class ) );
    }

    public static function autenticacao(){
        return new AutenticacaoMiddleware();
    }

    public static function corpoRequisicao( array $schema, string $contentType = 'application/json' ){
        return new CorpoRequisicaoMiddleware( $contentType, $schema );
    }
}
