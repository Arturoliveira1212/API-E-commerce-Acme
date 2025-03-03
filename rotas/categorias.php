<?php

use app\classes\Categoria;
use app\classes\GerenciadorRecurso;
use app\classes\factory\MiddlewareFactory;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Routing\RouteCollectorProxy;

$app->group( '/categorias', function( RouteCollectorProxy $group ){
    $corpoRequisicaoSalvarCategoria = [
        'nome' => 'string',
        'descricao' => 'string'
    ];

    // ROTAS PRIVADAS
    $group->post( '', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Categoria::class, 'novo', $request, $response, $args );
    } )
        ->add( MiddlewareFactory::corpoRequisicao( $corpoRequisicaoSalvarCategoria ) )
        ->add( MiddlewareFactory::permissao( [ 'admin' ], [ 'Cadastrar Categoria' ] ) )
        ->add( MiddlewareFactory::autenticacao() );

    $group->put( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Categoria::class, 'editar', $request, $response, $args );
    } )
        ->add( MiddlewareFactory::corpoRequisicao( $corpoRequisicaoSalvarCategoria ) )
        ->add( MiddlewareFactory::permissao( [ 'admin' ], [ 'Editar Categoria' ] ) )
        ->add( MiddlewareFactory::autenticacao() );

    $group->delete( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Categoria::class, 'excluirComId', $request, $response, $args );
    } )
        ->add( MiddlewareFactory::permissao( [ 'admin' ], [ 'Excluir Categoria' ] ) )
        ->add( MiddlewareFactory::autenticacao() );

    // ROTAS PÚBLICAS
    $group->get( '', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Categoria::class, 'obterTodos', $request, $response, $args );
    } );

    $group->get( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Categoria::class, 'obterComId', $request, $response, $args );
    } );
} );