<?php

use app\classes\Administrador;
use app\classes\factory\ClassFactory;
use app\classes\GerenciadorRecurso;
use app\controllers\AdministradorController;
use app\middlewares\AutenticacaoMiddleware;
use app\middlewares\CorpoRequisicaoMiddleware;
use app\middlewares\PermissaoAdministradorMiddleware;
use app\services\AdministradorService;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Routing\RouteCollectorProxy;

$app->group( '/administradores', function( RouteCollectorProxy $group ){
    /** @var AdministradorService */
    $administradorService = ClassFactory::makeService( Administrador::class );

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
    ->add( new CorpoRequisicaoMiddleware( CONTENT_TYPE, $corpoRequisicaoSalvarAdministrador ) )
    ->add( new PermissaoAdministradorMiddleware( [ 'Cadastrar Administrador' ], $administradorService ) )
    ->add( new AutenticacaoMiddleware() );

    $group->post( '/{id}/permissoes', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Administrador::class, 'adicionarPermissoes', $request, $response, $args );
    })
    ->add( new CorpoRequisicaoMiddleware( CONTENT_TYPE, $corpoRequisicaoSalvarPermissoes ) )
    ->add( new PermissaoAdministradorMiddleware( [ 'Adicionar Permissão para Administrador' ], $administradorService ) )
    ->add( new AutenticacaoMiddleware() );

    $group->put( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Administrador::class, 'editar', $request, $response, $args );
    } )
    ->add( new CorpoRequisicaoMiddleware( CONTENT_TYPE, $corpoRequisicaoSalvarAdministrador ) )
    ->add( new PermissaoAdministradorMiddleware( [ 'Editar Administrador' ], $administradorService ) )
    ->add( new AutenticacaoMiddleware() );

    $group->delete( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Administrador::class, 'excluirComId', $request, $response, $args );
    } )
    ->add( new PermissaoAdministradorMiddleware( [ 'Excluir Administrador' ], $administradorService ) )
    ->add( new AutenticacaoMiddleware() );

    // ROTAS PÚBLICAS
    $group->post( '/login', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Administrador::class, 'login', $request, $response, $args );
    } )
    ->add( new CorpoRequisicaoMiddleware( CONTENT_TYPE, $corpoRequisicaoLogin ) );

    $group->get( '', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Administrador::class, 'obterTodos', $request, $response, $args );
    } );

    $group->get( '/{id}', function( Request $request, Response $response, $args ){
        return GerenciadorRecurso::executar( Administrador::class, 'obterComId', $request, $response, $args );
    } );
} );