<?php

use Slim\Psr7\Response;
use app\classes\Cliente;
use app\classes\Administrador;
use app\classes\jwt\PayloadJWT;
use app\services\ClienteService;
use app\classes\http\HttpStatusCode;
use app\services\AdministradorService;
use app\middlewares\PermissaoMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

describe( 'PermissaoMiddleware', function(){
    beforeEach( function(){
        $this->request = Mockery::mock( ServerRequestInterface::class );
        $this->handler = Mockery::mock( RequestHandlerInterface::class );
        $this->payloadJWT = Mockery::mock( PayloadJWT::class );
        $this->administradorService = Mockery::mock( AdministradorService::class );
        $this->clienteService = Mockery::mock( ClienteService::class );
    });

    it( 'Retorna erro(403) quando o papel não está entre os papéis permitidos', function(){
        $middleware = new PermissaoMiddleware( [ 'admin' ], $this->administradorService, $this->clienteService, [ 'Cadastrar Categoria' ] );
        $payloadJWT = new PayloadJWT( 0, 'name', 'role', 1, 1 );

        $this->request->shouldReceive('getAttribute')->with('payloadJWT')->andReturn( $payloadJWT );
        allow($middleware)->toReceive('obterIdURL')->andReturn( 0 );

        $response = $middleware( $this->request, $this->handler );

        validarErroMiddleware( $response, HttpStatusCode::FORBIDDEN, [
            'sucess' => false,
            'message' => 'Você não tem permissão para realizar essa ação.'
        ] );
    });

    it( 'Retorna erro(403) quando o administrador não é encontrado', function(){
        $middleware = new PermissaoMiddleware( [ 'admin' ], $this->administradorService, $this->clienteService, [ 'Cadastrar Categoria' ] );
        $payloadJWT = new PayloadJWT( 0, 'name', 'admin', 1, 1 );

        $this->request->shouldReceive('getAttribute')->with('payloadJWT')->andReturn( $payloadJWT );
        $this->administradorService->shouldReceive('obterComId')->andReturn( [] );
        allow($middleware)->toReceive('obterIdURL')->andReturn( 0 );

        $response = $middleware( $this->request, $this->handler );

        validarErroMiddleware( $response, HttpStatusCode::FORBIDDEN, [
            'sucess' => false,
            'message' => 'Você não tem permissão para realizar essa ação.'
        ] );
    });

    it( 'Retorna erro(403) quando o cliente não é encontrado', function(){
        $middleware = new PermissaoMiddleware( [ 'cliente' ], $this->administradorService, $this->clienteService, [] );
        $payloadJWT = new PayloadJWT( 0, 'name', 'cliente', 1, 1 );

        $this->request->shouldReceive('getAttribute')->with('payloadJWT')->andReturn( $payloadJWT );
        $this->clienteService->shouldReceive('obterComId')->andReturn( [] );
        allow($middleware)->toReceive('obterIdURL')->andReturn( [] );

        $response = $middleware( $this->request, $this->handler );

        validarErroMiddleware( $response, HttpStatusCode::FORBIDDEN, [
            'sucess' => false,
            'message' => 'Você não tem permissão para realizar essa ação.'
        ] );
    });

    it( 'Retorna erro(403) quando o administrador não possui as permissões necessárias', function(){
        $permissoesNecessarias = [ 'Cadastrar Administrador' ];
        $permissoesDoAdministrador = [];

        $middleware = new PermissaoMiddleware( [ 'admin' ], $this->administradorService, $this->clienteService, $permissoesNecessarias );
        $payloadJWT = new PayloadJWT( 0, 'name', 'admin', 1, 1 );
        $administrador = new Administrador( 0, 'Artur Alves', 'artur@gmail.com', 12345678, $permissoesDoAdministrador );

        $this->request->shouldReceive('getAttribute')->with('payloadJWT')->andReturn( $payloadJWT );
        $this->administradorService->shouldReceive('obterComId')->andReturn( $administrador );
        allow($middleware)->toReceive('obterIdURL')->andReturn( 0 );

        $response = $middleware( $this->request, $this->handler );

        validarErroMiddleware( $response, HttpStatusCode::FORBIDDEN, [
            'sucess' => false,
            'message' => 'Você não tem permissão para realizar essa ação.'
        ] );
    });

    it( 'Retorna erro(403) quando o cliente do token é diferente do cliente da url', function(){
        $idUrl = 25;
        $idToken = 5;
        $middleware = new PermissaoMiddleware( [ 'cliente' ], $this->administradorService, $this->clienteService, [] );
        $payloadJWT = new PayloadJWT( $idToken, 'name', 'cliente', 1, 1 );

        $this->request->shouldReceive('getAttribute')->with('payloadJWT')->andReturn( $payloadJWT );
        $this->clienteService->shouldReceive('obterComId')->andReturn( new Cliente( $idToken ) );
        allow($middleware)->toReceive('obterIdURL')->andReturn( $idUrl );

        $response = $middleware( $this->request, $this->handler );

        validarErroMiddleware( $response, HttpStatusCode::FORBIDDEN, [
            'sucess' => false,
            'message' => 'Você não tem permissão para realizar essa ação.'
        ] );
    });

    it( 'Deve continuar a execução quando o administrador tem permissão', function(){
        $permissoesNecessarias = [ 'Cadastrar Administrador' ];
        $permissoesDoAdministrador = [ 'Cadastrar Administrador' ];

        $middleware = new PermissaoMiddleware( [ 'admin' ], $this->administradorService, $this->clienteService, $permissoesNecessarias );
        $payloadJWT = new PayloadJWT( 0, 'name', 'admin', 1, 1 );
        $administrador = new Administrador( 0, 'Artur Alves', 'artur@gmail.com', 12345678, $permissoesDoAdministrador );

        $this->request->shouldReceive('getAttribute')->with('payloadJWT')->andReturn( $payloadJWT );
        $this->administradorService->shouldReceive('obterComId')->andReturn( $administrador );
        allow($middleware)->toReceive('obterIdURL')->andReturn( 0 );
        $this->handler->shouldReceive('handle')->with( $this->request )->andReturn( new Response() );

        $response = $middleware( $this->request, $this->handler );

        expect( $response->getStatusCode() )->toEqual( HttpStatusCode::OK );
    });
});