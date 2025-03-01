<?php

use app\classes\Administrador;
use app\classes\factory\ClassFactory;
use app\classes\GerenciadorRecurso;
use app\middlewares\AutenticacaoMiddleware;
use app\middlewares\CorpoRequisicaoMiddleware;
use app\middlewares\PermissaoAdministradorMiddleware;
use app\services\AdministradorService;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

$app->group( '/administradores', function( $group ){
    /** @var AdministradorService */
    $administradorService = ClassFactory::makeService( Administrador::class );

    $group->post( '', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Administrador::class, 'novo', $request, $response, $args );
    } )
    ->add( new CorpoRequisicaoMiddleware( CONTENT_TYPE, [
        'nome' => 'string',
        'email' => 'string',
        'senha' => 'string'
    ] ) )
    ->add( new PermissaoAdministradorMiddleware( [ 'Cadastrar Administrador' ], $administradorService ) )
    ->add( new AutenticacaoMiddleware() );

    $group->post( '/login', function( $request, $response, $args ){
        return GerenciadorRecurso::executar( Administrador::class, 'login', $request, $response, $args );
    } )
    ->add( new CorpoRequisicaoMiddleware( CONTENT_TYPE, [
        'email' => 'string',
        'senha' => 'string'
    ] ) );

    $group->put( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Administrador::class, 'editar', $request, $response, $args );
    } )
    ->add( new CorpoRequisicaoMiddleware( CONTENT_TYPE, [
        'nome' => 'string',
        'email' => 'string',
        'senha' => 'string'
    ] ) )
    ->add( new PermissaoAdministradorMiddleware( [ 'Editar Administrador' ], $administradorService ) )
    ->add( new AutenticacaoMiddleware() );

    $group->get( '', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Administrador::class, 'obterTodos', $request, $response, $args );
    } );

    $group->get( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Administrador::class, 'obterComId', $request, $response, $args );
    } );

    $group->delete( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Administrador::class, 'excluirComId', $request, $response, $args );
    } )
    ->add( new PermissaoAdministradorMiddleware( [ 'Excluir Administrador' ], $administradorService ) )
    ->add( new AutenticacaoMiddleware() );

} );
