<?php

use app\classes\factory\MiddlewareFactory;
use app\classes\GerenciadorRecurso;
use app\classes\Produto;
use app\classes\TipoPermissao;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Routing\RouteCollectorProxy;

$app->group( '/produtos', function( RouteCollectorProxy $group ){
    $corpoRequisicaoSalvarProduto = [
        'nome' => 'string',
        'referencia' => 'string',
        'cor' => 'string',
        'preco' => 'float',
        'descricao' => 'string',
        'categoria' => 'int',
        'pesoEmGramas' => 'float'
    ];

    // ROTAS PRIVADAS
    $group->post( '', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Produto::class, 'novo', $request, $response, $args );
    } )
        ->add( MiddlewareFactory::corpoRequisicao( $corpoRequisicaoSalvarProduto ) )
        ->add( MiddlewareFactory::permissao( new TipoPermissao( 'admin', 'permissaoAdministrador', [ 'Cadastrar Produto' ] ) ) )
        ->add( MiddlewareFactory::autenticacao() );

    $group->put( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Produto::class, 'editar', $request, $response, $args );
    } )
        ->add( MiddlewareFactory::corpoRequisicao( $corpoRequisicaoSalvarProduto ) )
        ->add( MiddlewareFactory::permissao( new TipoPermissao( 'admin', 'permissaoAdministrador', [ 'Editar Produto' ] ) ) )
        ->add( MiddlewareFactory::autenticacao() );

    $group->delete( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Produto::class, 'excluirComId', $request, $response, $args );
    } )
        ->add( MiddlewareFactory::permissao( new TipoPermissao( 'admin', 'permissaoAdministrador', [ 'Excluir Produto' ] ) ) )
        ->add( MiddlewareFactory::autenticacao() );

    // ROTAS PÚBLICAS
    $group->get( '', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Produto::class, 'obterTodos', $request, $response, $args );
    } );

    $group->get( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Produto::class, 'obterComId', $request, $response, $args );
    } );

    $group->get( '/referencia/{referencia}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Produto::class, 'obterComReferencia', $request, $response, $args );
    } );
} );