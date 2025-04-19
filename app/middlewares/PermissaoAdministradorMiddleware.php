<?php

namespace app\middlewares;

use app\classes\Administrador;
use Slim\Psr7\Response;
use app\classes\http\RespostaHttp;
use app\classes\http\HttpStatusCode;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PermissaoAdministradorMiddleware
{
    private $permissoesNecessariasAdministrador;
    private $administradorService;

    public function __construct(
        array $permissoesNecessariasAdministrador,
        $administradorService
    ) {
        $this->administradorService = $administradorService;
        $this->permissoesNecessariasAdministrador = $permissoesNecessariasAdministrador;
    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var PayloadJWT */
        $payloadJWT = $request->getAttribute('payloadJWT');
        $idToken = $payloadJWT->sub();

        $administrador = $this->administradorService->obterComId($idToken);
        if (! $administrador instanceof Administrador || ! $this->validarPermissoes($administrador)) {
            return $this->semPermissao();
        }

        return $handler->handle($request);
    }

    private function validarPermissoes(Administrador $administrador): bool
    {
        foreach ($this->permissoesNecessariasAdministrador as $permissao) {
            if (! $administrador->possuiPermissao($permissao)) {
                return false;
            }
        }
        return true;
    }

    private function semPermissao(string $mensagem = 'Você não tem permissão para realizar essa ação.')
    {
        return RespostaHttp::enviarResposta(new Response(), HttpStatusCode::FORBIDDEN, [
            'message' => $mensagem
        ]);
    }
}
