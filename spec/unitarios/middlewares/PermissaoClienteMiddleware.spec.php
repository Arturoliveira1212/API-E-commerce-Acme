<?php

use Slim\Psr7\Response;
use app\classes\Cliente;
use app\classes\jwt\PayloadJWT;
use app\classes\http\HttpStatusCode;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use app\middlewares\PermissaoClienteMiddleware;
use app\services\ClienteService;

describe( 'PermissaoClienteMiddleware', function(){
    beforeEach( function(){
        $this->request = Mockery::mock( ServerRequestInterface::class );
        $this->handler = Mockery::mock( RequestHandlerInterface::class );
        $this->payloadJWT = Mockery::mock( PayloadJWT::class );
        $this->clienteService = Mockery::mock( ClienteService::class );
        $this->middleware = new PermissaoClienteMiddleware( $this->clienteService );
    });

    function validarErroMiddleware( $response, int $status, array $respostaEmArrayEsperada ){
        $respostaEmArray = json_decode( $response->getBody(), true );

        expect( $response )->toBeAnInstanceOf( Response::class );
        expect( $response->getStatusCode() )->toEqual( $status );
        expect( $respostaEmArray )->toBeA( 'array' );
        expect( $respostaEmArray)->toContainKeys( array_keys( $respostaEmArrayEsperada ) );
        expect( $respostaEmArray )->toEqual( $respostaEmArrayEsperada );
    }

    it( 'Retorna erro(403) quando o cliente não é encontrado', function(){
        $payloadJWT = new PayloadJWT( 0, 'name', 'cliente', 1, 1 );

        $this->request->shouldReceive('getAttribute')->with('payloadJWT')->andReturn( $payloadJWT );
        $this->clienteService->shouldReceive('obterComId')->andReturn( [] );
        allow($this->middleware)->toReceive('obterIdURL')->andReturn( [] );

        $response = $this->middleware( $this->request, $this->handler );

        validarErroMiddleware( $response, HttpStatusCode::FORBIDDEN, [
            'sucess' => false,
            'message' => 'Você não tem permissão para realizar essa ação.'
        ] );
    });

    it( 'Retorna erro(403) quando o cliente do token é diferente do cliente da url', function(){
        $idUrl = 25;
        $idToken = 5;
        $payloadJWT = new PayloadJWT( $idToken, 'name', 'cliente', 1, 1 );

        $this->request->shouldReceive('getAttribute')->with('payloadJWT')->andReturn( $payloadJWT );
        $this->clienteService->shouldReceive('obterComId')->andReturn( new Cliente( $idToken ) );
        allow($this->middleware)->toReceive('obterIdURL')->andReturn( $idUrl );

        $response = $this->middleware( $this->request, $this->handler );

        validarErroMiddleware( $response, HttpStatusCode::FORBIDDEN, [
            'sucess' => false,
            'message' => 'Você não tem permissão para realizar essa ação.'
        ] );
    });

    it( 'Deve continuar a execução quando o cliente é válido', function(){
        $idUrl = 5;
        $idToken = 5;
        $payloadJWT = new PayloadJWT( $idToken, 'name', 'cliente', 1, 1 );

        $this->request->shouldReceive('getAttribute')->with('payloadJWT')->andReturn( $payloadJWT );
        $this->clienteService->shouldReceive('obterComId')->andReturn( new Cliente( $idToken ) );
        allow($this->middleware)->toReceive('obterIdURL')->andReturn( $idUrl );

        $this->handler->shouldReceive('handle')->with( $this->request )->andReturn( new Response() );

        $response = $this->middleware( $this->request, $this->handler );
        expect( $response->getStatusCode() )->toEqual( HttpStatusCode::OK );
    });
});