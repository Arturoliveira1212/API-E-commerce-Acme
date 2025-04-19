<?php

namespace app\middlewares;

use app\classes\Cliente;
use Slim\Psr7\Response;
use app\classes\jwt\PayloadJWT;
use app\classes\http\RespostaHttp;
use app\classes\http\HttpStatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;

class PermissaoPedidoMiddleware
{
    private $clienteService;

    public function __construct($clienteService)
    {
        $this->clienteService = $clienteService;
    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var PayloadJWT */
        $payloadJWT = $request->getAttribute('payloadJWT');
        $idToken = $payloadJWT->sub();

        $cliente = $this->clienteService->obterComId($idToken);
        if (! $cliente instanceof Cliente) {
            return $this->semPermissao();
        }

        return $handler->handle($request);
    }

    private function semPermissao(string $mensagem = 'Você não tem permissão para realizar essa ação.')
    {
        return RespostaHttp::enviarResposta(new Response(), HttpStatusCode::FORBIDDEN, [
            'message' => $mensagem
        ]);
    }
}
