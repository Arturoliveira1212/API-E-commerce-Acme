<?php

use app\classes\Cliente;
use app\classes\factory\MiddlewareFactory;
use app\classes\GerenciadorRecurso;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Routing\RouteCollectorProxy;

$app->group( '/clientes', function( RouteCollectorProxy $group ){
    $corpoRequisicaoSalvarCliente = [
        'nome' => 'string',
        'email' => 'string',
        'senha' => 'string'
    ];

    $corpoRequisicaoLogin = [
        'email' => 'string',
        'senha' => 'string'
    ];

    // ROTAS PRIVADAS
    $group->put( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Cliente::class, 'editar', $request, $response, $args );
    } )
        ->add( MiddlewareFactory::corpoRequisicao( $corpoRequisicaoSalvarCliente ) )
        ->add( MiddlewareFactory::permissao( [ 'admin', 'cliente' ], [ 'Editar Cliente' ] ) )
        ->add( MiddlewareFactory::autenticacao() );

    $group->delete( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Cliente::class, 'excluirComId', $request, $response, $args );
    } )
        ->add( MiddlewareFactory::permissao( [ 'admin', 'cliente' ], [ 'Excluir Cliente' ] ) )
        ->add( MiddlewareFactory::autenticacao() );

    // ROTAS PÚBLICAS
    $group->post( '', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Cliente::class, 'novo', $request, $response, $args );
    } )
        ->add( MiddlewareFactory::corpoRequisicao( $corpoRequisicaoSalvarCliente ) );

    $group->post( '/login', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Cliente::class, 'login', $request, $response, $args );
    } )
        ->add( MiddlewareFactory::corpoRequisicao( $corpoRequisicaoLogin ) );

    $group->get( '', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Cliente::class, 'obterTodos', $request, $response, $args );
    } );

    $group->get( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Cliente::class, 'obterComId', $request, $response, $args );
    } );
} );