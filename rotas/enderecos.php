<?php

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use app\classes\Endereco;
use app\classes\GerenciadorRecurso;
use Slim\Routing\RouteCollectorProxy;
use app\classes\factory\MiddlewareFactory;

$corpoRequisicaoSalvarEndereco = [
    'logradouro' => 'string',
    'cidade' => 'string',
    'bairro' => 'string',
    'numero' => 'string',
    'cep' => 'string',
];

$app->group( '/enderecos', function( RouteCollectorProxy $group ) use ( $corpoRequisicaoSalvarEndereco ){
    $group->put( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Endereco::class, 'editar', $request, $response, $args );
    } )
        ->add( MiddlewareFactory::corpoRequisicao( $corpoRequisicaoSalvarEndereco ) )
        ->add( MiddlewareFactory::permissao( [ 'admin', 'cliente' ], [ 'Editar Endereço' ] ) )
        ->add( MiddlewareFactory::autenticacao() );

    $group->delete( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Endereco::class, 'excluirComId', $request, $response, $args );
    } )
        ->add( MiddlewareFactory::permissao( [ 'admin', 'cliente' ], [ 'Excluir Endereço' ] ) )
        ->add( MiddlewareFactory::autenticacao() );

    $group->get( '', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Endereco::class, 'obterTodos', $request, $response, $args );
    } );

    $group->get( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Endereco::class, 'obterComId', $request, $response, $args );
    } );
});

$app->post( '/clientes/{idRecursoPai}/enderecos', function( Request $request, Response $response, $args ){
    return GerenciadorRecurso::executar( Endereco::class, 'novo', $request, $response, $args );
} )
    ->add( MiddlewareFactory::corpoRequisicao( $corpoRequisicaoSalvarEndereco ) )
    ->add( MiddlewareFactory::permissao( [ 'admin', 'cliente' ], [ 'Cadastrar Endereço Endereço' ] ) )
    ->add( MiddlewareFactory::autenticacao() );

$app->get( '/clientes/{idRecursoPai}/enderecos', function( Request $request, Response $response, $args ){
    return GerenciadorRecurso::executar( Endereco::class, 'obterEnderecosDoCliente', $request, $response, $args );
} );