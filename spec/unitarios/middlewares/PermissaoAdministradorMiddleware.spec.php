<?php

use Slim\Psr7\Response;
use app\classes\Administrador;
use app\classes\jwt\PayloadJWT;
use app\classes\http\HttpStatusCode;
use app\services\AdministradorService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use app\middlewares\PermissaoAdministradorMiddleware;

describe('PermissaoAdministradorMiddleware', function () {
    beforeEach(function () {
        $this->request = Mockery::mock(ServerRequestInterface::class);
        $this->handler = Mockery::mock(RequestHandlerInterface::class);
        $this->payloadJWT = Mockery::mock(PayloadJWT::class);
    });

    it('Deve retornar erro(403) quando o administrador não é encontrado', function () {
        $administradorService = Mockery::mock(AdministradorService::class);
        $middleware = new PermissaoAdministradorMiddleware([], $administradorService);
        $payloadJWT = new PayloadJWT(0, 'name', 'role', 1, 1);

        $this->request->shouldReceive('getAttribute')->with('payloadJWT')->andReturn($payloadJWT);
        $administradorService->shouldReceive('obterComId')->with($payloadJWT->sub())->andReturn(null);

        $response = $middleware($this->request, $this->handler);

        validarErroMiddleware($response, HttpStatusCode::FORBIDDEN, [
            'sucess' => false,
            'message' => 'Você não tem permissão para realizar essa ação.'
        ]);
    });

    it('Deve retornar erro(403) quando o administrador não possui as permissões necessárias', function () {
        $permissoesNecessarias = [ 'Cadastrar Administrador' ];
        $permissoesDoAdministrador = [];

        $administradorService = Mockery::mock(AdministradorService::class);
        $middleware = new PermissaoAdministradorMiddleware($permissoesNecessarias, $administradorService);
        $payloadJWT = new PayloadJWT(0, 'name', 'role', 1, 1);
        $administrador = new Administrador(0, 'Artur Alves', 'artur@gmail.com', 12345678, $permissoesDoAdministrador);

        $this->request->shouldReceive('getAttribute')->with('payloadJWT')->andReturn($payloadJWT);
        $administradorService->shouldReceive('obterComId')->with($payloadJWT->sub())->andReturn($administrador);

        $response = $middleware($this->request, $this->handler);

        validarErroMiddleware($response, HttpStatusCode::FORBIDDEN, [
            'sucess' => false,
            'message' => 'Você não tem permissão para realizar essa ação.'
        ]);
    });

    it('Deve continuar a execução quando o administrador tem permissão', function () {
        $permissoesNecessarias = [ 'Cadastrar Administrador' ];
        $permissoesDoAdministrador = [ 'Cadastrar Administrador' ];

        $administradorService = Mockery::mock(AdministradorService::class);
        $middleware = new PermissaoAdministradorMiddleware($permissoesNecessarias, $administradorService);
        $payloadJWT = new PayloadJWT(0, 'name', 'role', 1, 1);
        $administrador = new Administrador(0, 'Artur Alves', 'artur@gmail.com', 12345678, $permissoesDoAdministrador);

        $this->request->shouldReceive('getAttribute')->with('payloadJWT')->andReturn($payloadJWT);
        $administradorService->shouldReceive('obterComId')->with($payloadJWT->sub())->andReturn($administrador);

        $this->handler->shouldReceive('handle')->with($this->request)->andReturn(new Response());

        $response = $middleware($this->request, $this->handler);
        expect($response->getStatusCode())->toEqual(HttpStatusCode::OK);
    });
});
