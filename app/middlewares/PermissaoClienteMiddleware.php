<?php

namespace app\middlewares;

use app\classes\Administrador;
use app\classes\Cliente;
use Slim\Psr7\Response;
use app\classes\jwt\PayloadJWT;
use app\classes\http\RespostaHttp;
use app\classes\http\HttpStatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;

class PermissaoClienteMiddleware {
    private $clienteService;

    public function __construct( $clienteService ){
        $this->clienteService = $clienteService;
    }

    public function __invoke( ServerRequestInterface $request, RequestHandlerInterface $handler ): ResponseInterface {
        /** @var PayloadJWT */
        $payloadJWT = $request->getAttribute('payloadJWT');
        $idToken = $payloadJWT->sub();
        $idUrl = $this->obterIdURL( $request );

        $cliente = $this->clienteService->obterComId( $idToken );
        if( ! $cliente instanceof Cliente || $cliente->getId() != $idUrl ){
            return $this->semPermissao();
        }

        return $handler->handle( $request );
    }

    private function obterIdURL( ServerRequestInterface $request ){
        $routeContext = RouteContext::fromRequest( $request );
        $route = $routeContext->getRoute();
        $idUrl = $route->getArguments()['id'] ?? 0;

        return intval( $idUrl );
    }

    private function semPermissao( string $mensagem = 'Você não tem permissão para realizar essa ação.' ){
        return RespostaHttp::enviarResposta( new Response(), HttpStatusCode::FORBIDDEN, [
            'message' => $mensagem
        ] );
    }
}