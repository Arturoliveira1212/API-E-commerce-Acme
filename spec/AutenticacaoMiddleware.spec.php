<?php

use Mockery;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use app\middlewares\AutenticacaoMiddleware;
use app\classes\jwt\PayloadJWT;
use app\classes\http\HttpStatusCode;

describe('AutenticacaoMiddleware', function () {
    beforeEach(function () {
        $this->middleware = new AutenticacaoMiddleware();
        $this->request = Mockery::mock(ServerRequestInterface::class);
        $this->handler = Mockery::mock(RequestHandlerInterface::class);
        $this->payloadJWT = Mockery::mock(PayloadJWT::class);
    });

    it('Deve retornar 401 quando o token não é enviado', function () {
        $this->request->shouldReceive('getHeaderLine')->with('Authorization')->andReturn('');

        $resposta = $this->middleware->__invoke($this->request, $this->handler);
        expect($resposta->getStatusCode())->toEqual(HttpStatusCode::UNAUTHORIZED);
    });

    it('Deve retornar 401 quando o token é inválido', function () {
        $this->request->shouldReceive('getHeaderLine')->with('Authorization')->andReturn('Bearer token_invalido');

        allow($this->middleware)->toReceive('decodificarToken')->andReturn(null);

        $resposta = $this->middleware->__invoke($this->request, $this->handler);
        expect($resposta->getStatusCode())->toEqual(HttpStatusCode::UNAUTHORIZED);
    });

    it('Deve continuar a execução quando o token é válido', function () {
        $this->request->shouldReceive('getHeaderLine')->with('Authorization')->andReturn('Bearer token_valido');

        allow($this->middleware)->toReceive('decodificarToken')->andReturn($this->payloadJWT);

        $this->request->shouldReceive('withAttribute')->with('payloadJWT', $this->payloadJWT)->andReturn($this->request);

        $this->handler->shouldReceive('handle')->with($this->request)->andReturn(new Response());

        $resposta = $this->middleware->__invoke($this->request, $this->handler);
        expect($resposta->getStatusCode())->toEqual(200);
    });
});
