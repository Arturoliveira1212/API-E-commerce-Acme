<?php

use app\classes\GerenciadorRecurso;
use app\controllers\ClienteController;

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