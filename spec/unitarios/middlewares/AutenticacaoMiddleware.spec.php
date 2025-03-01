<?php

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use app\middlewares\AutenticacaoMiddleware;
use app\classes\jwt\PayloadJWT;
use app\classes\http\HttpStatusCode;

describe( 'AutenticacaoMiddleware', function(){
    beforeEach( function(){
        $this->middleware = new AutenticacaoMiddleware();
        $this->request = Mockery::mock( ServerRequestInterface::class );
        $this->handler = Mockery::mock( RequestHandlerInterface::class );
        $this->payloadJWT = Mockery::mock( PayloadJWT::class );
    });

    function validarErroMiddleware( $response, int $status, array $respostaEmArrayEsperada ){
        $respostaEmArray = json_decode( $response->getBody(), true );

        expect( $response )->toBeAnInstanceOf( Response::class );
        expect( $response->getStatusCode() )->toEqual( $status );
        expect( $respostaEmArray )->toBeA( 'array' );
        expect( $respostaEmArray)->toContainKeys( array_keys( $respostaEmArrayEsperada ) );
        expect( $respostaEmArray )->toEqual( $respostaEmArrayEsperada );
    }

    it( 'Deve retornar erro(401) quando o token não é enviado', function(){
        $this->request->shouldReceive('getHeaderLine')->with('Authorization')->andReturn('');
        $response = $this->middleware( $this->request, $this->handler );

        validarErroMiddleware( $response, HttpStatusCode::UNAUTHORIZED, [
            'sucess' => false,
            'message' => 'Token de autenticação não foi enviado.'
        ] );
    });

    it( 'Deve retornar erro(401) quando token fora do padrão é enviado', function(){
        $this->request->shouldReceive('getHeaderLine')->with('Authorization')->andReturn('Bearer');
        $response = $this->middleware( $this->request, $this->handler );

        validarErroMiddleware( $response, HttpStatusCode::UNAUTHORIZED, [
            'sucess' => false,
            'message' => 'Token de autenticação não foi enviado.'
        ] );
    });

    it( 'Deve retornar erro(401) quando token é inválido', function(){
        $this->request->shouldReceive('getHeaderLine')->with('Authorization')->andReturn('Bearer Token Inválido');
        allow( $this->middleware )->toReceive('decodificarToken')->andReturn( null );
        $response = $this->middleware( $this->request, $this->handler );

        validarErroMiddleware( $response, HttpStatusCode::UNAUTHORIZED, [
            'sucess' => false,
            'message' => 'Token de autenticação inválido.'
        ] );
    });

    it( 'Deve continuar a execução quando o token é válido', function(){
        $this->request->shouldReceive('getHeaderLine')->with('Authorization')->andReturn('Bearer Token Válido');
        allow( $this->middleware )->toReceive('decodificarToken')->andReturn( $this->payloadJWT );

        $this->request->shouldReceive('withAttribute')->with( 'payloadJWT', $this->payloadJWT )->andReturn( $this->request );
        $this->handler->shouldReceive('handle')->with( $this->request )->andReturn( new Response() );

        $response = $this->middleware( $this->request, $this->handler );
        expect( $response->getStatusCode() )->toEqual( HttpStatusCode::OK );
    });
});
