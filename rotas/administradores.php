<?php

use app\classes\GerenciadorRecurso;
use app\controllers\AdministradorController;
use app\middlewares\AutenticacaoMiddleware;
use app\middlewares\CorpoRequisicaoMiddleware;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

$app->group( '/administradores', function( $group ){
    $group->post( '', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( AdministradorController::class, 'novo', $request, $response, $args );
    } )
    ->add( new CorpoRequisicaoMiddleware( CONTENT_TYPE, [
        'nome' => 'string',
        'email' => 'string',
        'senha' => 'string'
    ] ) );

    $group->post( '/login', function( $request, $response, $args ){
        return GerenciadorRecurso::executar( AdministradorController::class, 'login', $request, $response, $args );
    } )
    ->add( new CorpoRequisicaoMiddleware( CONTENT_TYPE, [
        'email' => 'string',
        'senha' => 'string'
    ] ) );

    $group->put( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( AdministradorController::class, 'editar', $request, $response, $args );
    } )
    ->add( new AutenticacaoMiddleware() )
    ->add( new CorpoRequisicaoMiddleware( CONTENT_TYPE, [
        'nome' => 'string',
        'email' => 'string',
        'senha' => 'string'
    ] ) );

    $group->get( '', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( AdministradorController::class, 'obterTodos', $request, $response, $args );
    } );

    $group->get( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( AdministradorController::class, 'obterComId', $request, $response, $args );
    } );

    $group->delete( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( AdministradorController::class, 'excluirComId', $request, $response, $args );
    } )
    ->add( new AutenticacaoMiddleware() );
} );
