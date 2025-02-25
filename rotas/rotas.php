<?php

use app\classes\GerenciadorRecurso;
use app\controllers\AdministradorController;
use app\controllers\CategoriaController;
use app\middlewares\CorpoRequisicaoMiddleware;

const CONTENT_TYPE = 'application/json';

$app->group( '/administradores', function( $group ){
    $corpoRequisicaoMiddleware = new CorpoRequisicaoMiddleware( CONTENT_TYPE, [
        'e-mail' => 'string',
        'senha' => 'string'
    ] );

    $group->post( '/login', function( $request, $response, $args ){
        return GerenciadorRecurso::executar( AdministradorController::class, 'login', $request, $response, $args );
    } )->add( $corpoRequisicaoMiddleware );
} );

$app->group( '/clientes', function( $group ){

    $group->post( '', function( $request, $response, $args ){
        return GerenciadorRecurso::executar( ClienteController::class, 'novo', $request, $response, $args );
    } );

    $group->post( '/login', function( $request, $response, $args ){
        return GerenciadorRecurso::executar( ClienteController::class, 'login', $request, $response, $args );
    } );

    $group->put( '/{id}', function( $request, $response, $args ){
        return GerenciadorRecurso::executar( ClienteController::class, 'editar', $request, $response, $args );
    } );

    $group->get( '', function( $request, $response, $args ){
        return GerenciadorRecurso::executar( ClienteController::class, 'obterTodos', $request, $response, $args );
    } );

    $group->get( '/{id}', function( $request, $response, $args ){
        return GerenciadorRecurso::executar( ClienteController::class, 'obterComId', $request, $response, $args );
    } );

    $group->delete( '/{id}', function( $request, $response, $args ){
        return GerenciadorRecurso::executar( ClienteController::class, 'exluirComId', $request, $response, $args );
    } );
} );

$app->group( '/categorias', function( $group ){
    $corpoRequisicaoMiddleware = new CorpoRequisicaoMiddleware( CONTENT_TYPE, [
        'nome' => 'string',
        'descricao' => 'string'
    ] );

    $group->post( '', function( $request, $response, $args ){
        return GerenciadorRecurso::executar( CategoriaController::class, 'novo', $request, $response, $args );
    } )->add( $corpoRequisicaoMiddleware );

    $group->put( '/{id}', function( $request, $response, $args ){
        return GerenciadorRecurso::executar( CategoriaController::class, 'editar', $request, $response, $args );
    } )->add( $corpoRequisicaoMiddleware );

    $group->get( '', function( $request, $response, $args ){
        return GerenciadorRecurso::executar( CategoriaController::class, 'obterTodos', $request, $response, $args );
    } );

    $group->get( '/{id}', function( $request, $response, $args ){
        return GerenciadorRecurso::executar( CategoriaController::class, 'obterComId', $request, $response, $args );
    } );

    $group->delete( '/{id}', function( $request, $response, $args ){
        return GerenciadorRecurso::executar( CategoriaController::class, 'exluirComId', $request, $response, $args );
    } );
} );