<?php

use app\classes\Categoria;
use app\classes\Administrador;
use app\classes\GerenciadorRecurso;
use app\classes\factory\ClassFactory;
use app\middlewares\AutenticacaoMiddleware;
use app\middlewares\CorpoRequisicaoMiddleware;
use app\middlewares\PermissaoAdministradorMiddleware;
use app\services\AdministradorService;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

$app->group( '/categorias', function( $group ){
    /** @var AdministradorService */
    $administradorService = ClassFactory::makeService( Administrador::class );

    $group->post( '', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Categoria::class, 'novo', $request, $response, $args );
    } )
    ->add( new CorpoRequisicaoMiddleware( CONTENT_TYPE, [
        'nome' => 'string',
        'descricao' => 'string'
        ] ) )
    ->add( new PermissaoAdministradorMiddleware( [ 'Cadastrar Categoria' ], $administradorService ) )
    ->add( new AutenticacaoMiddleware() );

    $group->put( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Categoria::class, 'editar', $request, $response, $args );
    } )
    ->add( new CorpoRequisicaoMiddleware( CONTENT_TYPE, [
        'nome' => 'string',
        'descricao' => 'string'
        ] ) )
    ->add( new PermissaoAdministradorMiddleware( [ 'Editar Categoria' ], $administradorService ) )
    ->add( new AutenticacaoMiddleware() );

    $group->get( '', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Categoria::class, 'obterTodos', $request, $response, $args );
    } );

    $group->get( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Categoria::class, 'obterComId', $request, $response, $args );
    } );

    $group->delete( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Categoria::class, 'excluirComId', $request, $response, $args );
    } )
    ->add( new AutenticacaoMiddleware() );
} );