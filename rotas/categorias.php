<?php

use app\classes\GerenciadorRecurso;
use app\controllers\CategoriaController;
use app\middlewares\AutenticacaoJWTMiddleware;
use app\middlewares\CorpoRequisicaoMiddleware;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

$app->group( '/categorias', function( $group ){
    $group->post( '', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( CategoriaController::class, 'novo', $request, $response, $args );
    } )
    ->add( new AutenticacaoJWTMiddleware() )
    ->add( new CorpoRequisicaoMiddleware( CONTENT_TYPE, [
        'nome' => 'string',
        'descricao' => 'string'
    ] ) );

    $group->put( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( CategoriaController::class, 'editar', $request, $response, $args );
    } )
    ->add( new AutenticacaoJWTMiddleware() )
    ->add( new CorpoRequisicaoMiddleware( CONTENT_TYPE, [
        'nome' => 'string',
        'descricao' => 'string'
    ] ) );

    $group->get( '', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( CategoriaController::class, 'obterTodos', $request, $response, $args );
    } );

    $group->get( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( CategoriaController::class, 'obterComId', $request, $response, $args );
    } );

    $group->delete( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( CategoriaController::class, 'exluirComId', $request, $response, $args );
    } )
    ->add( new AutenticacaoJWTMiddleware() );
} );