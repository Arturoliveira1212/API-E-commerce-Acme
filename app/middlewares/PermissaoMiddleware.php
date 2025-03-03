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

class PermissaoMiddleware {
    private $papeisPermitidos;
    private $permissoesNecessarias;
    private $administradorService;
    private $clienteService;

    public function __construct(
        array $papeisPermitidos,
        $administradorService,
        $clienteService,
        array $permissoesNecessarias = []
    ){
        $this->papeisPermitidos = $papeisPermitidos;
        $this->administradorService = $administradorService;
        $this->clienteService = $clienteService;
        $this->permissoesNecessarias = $permissoesNecessarias;
    }

    public function __invoke( ServerRequestInterface $request, RequestHandlerInterface $handler ): ResponseInterface {
        /** @var PayloadJWT */
        $payloadJWT = $request->getAttribute('payloadJWT');
        $papel = $payloadJWT->role();
        $idToken = $payloadJWT->sub();
        $idUrl = $this->obterIdURL( $request );

        if( ! in_array( $papel, $this->papeisPermitidos ) ){
            return $this->semPermissao();
        }

        if( $papel === 'admin' ){
            $usuario = $this->administradorService->obterComId( $idToken );
            if( ! $usuario instanceof Administrador || ! $this->validarPermissoes( $usuario ) ){
                return $this->semPermissao();
            }
        }

        if( $papel === 'cliente' ){
            $usuario = $this->clienteService->obterComId( $idToken );
            if( ! $usuario instanceof Cliente || $usuario->getId() !== $idUrl ){
                return $this->semPermissao();
            }
        }

        return $handler->handle( $request );
    }

    private function obterIdURL( ServerRequestInterface $request ){
        $routeContext = RouteContext::fromRequest( $request );
        $route = $routeContext->getRoute();
        $idUrl = $route->getArguments()['id'] ?? 0;

        return $idUrl;
    }

    private function validarPermissoes( Administrador $administrador ): bool {
        foreach( $this->permissoesNecessarias as $permissao ){
            if( ! $administrador->possuiPermissao( $permissao ) ){
                return false;
            }
        }
        return true;
    }

    private function semPermissao( string $mensagem = 'Você não tem permissão para realizar essa ação.' ){
        return RespostaHttp::enviarResposta( new Response(), HttpStatusCode::FORBIDDEN, [
            'message' => $mensagem
        ] );
    }
}