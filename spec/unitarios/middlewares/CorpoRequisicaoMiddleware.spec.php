<?php

use app\classes\http\HttpStatusCode;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use app\middlewares\CorpoRequisicaoMiddleware;
use Slim\Psr7\Response;

describe( 'CorpoRequisicaoMiddleware', function() {
    beforeEach(function () {
        $this->request = Mockery::mock( ServerRequestInterface::class );
        $this->handler = Mockery::mock( RequestHandlerInterface::class );
    });

    it( 'Retorna erro(400) quando o Content-Type é inválido.', function(){
        $middleware = new CorpoRequisicaoMiddleware( 'application/json', [ 'nome' => 'string' ] );

        $this->request->shouldReceive('getHeaderLine')->with('Content-Type')->andReturn('application/xml');
        $this->request->shouldReceive('getParsedBody')->andReturn( [ 'nome' => 'Artur' ] );
        $response = $middleware( $this->request, $this->handler );

        validarErroMiddleware( $response, HttpStatusCode::BAD_REQUEST, [
            'sucess' => false,
            'message' => 'O corpo da requisição deve ser em JSON válido.'
        ] );
    });

    it( 'Retorna erro(400) quando o corpo de requisição é vazio.', function(){
        $middleware = new CorpoRequisicaoMiddleware( 'application/json', [ 'nome' => 'string' ] );

        $this->request->shouldReceive('getHeaderLine')->with('Content-Type')->andReturn('application/json');
        $this->request->shouldReceive('getParsedBody')->andReturn( [] );
        $response = $middleware( $this->request, $this->handler );

        validarErroMiddleware( $response, HttpStatusCode::BAD_REQUEST, [
            'sucess' => false,
            'message' => 'O corpo da requisição deve ser em JSON válido.'
        ] );
    });

    it( 'Retorna erro(400) quando o corpo de requisição não tem os campos obrigatórios.', function(){
        $middleware = new CorpoRequisicaoMiddleware( 'application/json', [ 'nome' => 'string' ] );

        $this->request->shouldReceive('getHeaderLine')->with('Content-Type')->andReturn('application/json');
        $this->request->shouldReceive('getParsedBody')->andReturn( [ 'artur' => 'artur '] );
        $response = $middleware( $this->request, $this->handler );

        validarErroMiddleware( $response, HttpStatusCode::BAD_REQUEST, [
            'sucess' => false,
            'message' => 'O corpo da requisição é inválido.',
            'data' => [
                'erros' => [
                    'nome' => 'Campo nome não foi enviado.'
                ]
            ]
        ] );
    });

    it( 'Retorna erro(400) quando o corpo de requisição tem os campos obrigatórios mas de tipos inválidos.', function(){
        $middleware = new CorpoRequisicaoMiddleware( 'application/json', [ 'nome' => 'string' ] );

        $this->request->shouldReceive('getHeaderLine')->with('Content-Type')->andReturn('application/json');
        $this->request->shouldReceive('getParsedBody')->andReturn( [ 'nome' => 11111 ] );
        $response = $middleware( $this->request, $this->handler );

        validarErroMiddleware( $response, HttpStatusCode::BAD_REQUEST, [
            'sucess' => false,
            'message' => 'O corpo da requisição é inválido.',
            'data' => [
                'erros' => [
                    'nome' => 'Campo nome deve ser do tipo string.'
                ]
            ]
        ] );
    });

    it( 'Deve continuar a execução quando o corpo da requisição é válido', function(){
        $middleware = new CorpoRequisicaoMiddleware( 'application/json', [ 'nome' => 'string' ] );

        $this->request->shouldReceive('getHeaderLine')->with('Content-Type')->andReturn('application/json');
        $this->request->shouldReceive('getParsedBody')->andReturn( [ 'nome' => 'Artur Alves' ] );

        $this->handler->shouldReceive('handle')->with( $this->request )->andReturn( new Response() );
        $this->request->shouldReceive('withParsedBody')->with(['nome' => 'Artur Alves'])->andReturnSelf();

        $response = $middleware( $this->request, $this->handler );
        expect( $response->getStatusCode() )->toEqual( HttpStatusCode::OK );
    });
});