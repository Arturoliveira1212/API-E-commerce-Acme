<?php

use app\classes\Administrador;
use app\classes\GerenciadorRecurso;
use app\classes\factory\MiddlewareFactory;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Routing\RouteCollectorProxy;

$app->group( '/administradores', function( RouteCollectorProxy $group ){
    $corpoRequisicaoSalvarAdministrador = [
        'nome' => 'string',
        'email' => 'string',
        'senha' => 'string'
    ];

    $corpoRequisicaoLogin = [
        'email' => 'string',
        'senha' => 'string'
    ];

    $corpoRequisicaoSalvarPermissoes = [
        'permissoes' => 'array'
    ];

    // ROTAS PRIVADAS
    $group->post( '', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Administrador::class, 'novo', $request, $response, $args );
    } )
        ->add( MiddlewareFactory::corpoRequisicao( $corpoRequisicaoSalvarAdministrador ) )
        ->add( MiddlewareFactory::permissao( [ 'admin' ], [ 'Cadastrar Administrador' ] ) )
        ->add( MiddlewareFactory::autenticacao() );

    $group->post( '/{id}/permissoes', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Administrador::class, 'salvarPermissoes', $request, $response, $args );
    })
        ->add( MiddlewareFactory::corpoRequisicao( $corpoRequisicaoSalvarPermissoes ) )
        ->add( MiddlewareFactory::permissao( [ 'admin' ], [ 'Adicionar Permissão para Administrador' ] ) )
        ->add( MiddlewareFactory::autenticacao() );

    $group->put( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Administrador::class, 'editar', $request, $response, $args );
    } )
        ->add( MiddlewareFactory::corpoRequisicao( $corpoRequisicaoSalvarAdministrador ) )
        ->add( MiddlewareFactory::permissao( [ 'admin' ], [ 'Editar Administrador' ] ) )
        ->add( MiddlewareFactory::autenticacao() );

    $group->delete( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Administrador::class, 'excluirComId', $request, $response, $args );
    } )
        ->add( MiddlewareFactory::permissao( [ 'admin' ], [ 'Excluir Administrador' ] ) )
        ->add( MiddlewareFactory::autenticacao() );

    // ROTAS PÚBLICAS
    $group->post( '/login', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Administrador::class, 'login', $request, $response, $args );
    } )
        ->add( MiddlewareFactory::corpoRequisicao( $corpoRequisicaoLogin ) );

    $group->get( '', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Administrador::class, 'obterTodos', $request, $response, $args );
    } );

    $group->get( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Administrador::class, 'obterComId', $request, $response, $args );
    } );
} );