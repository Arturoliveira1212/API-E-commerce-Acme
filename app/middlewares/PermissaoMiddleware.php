<?php

namespace app\middlewares;

use app\classes\factory\MiddlewareFactory;
use Slim\Psr7\Response;
use app\classes\jwt\PayloadJWT;
use app\classes\http\RespostaHttp;
use app\classes\http\HttpStatusCode;
use app\classes\TipoPermissao;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PermissaoMiddleware
{
    private $tiposPermissao;

    public function __construct($tiposPermissao)
    {
        $this->tiposPermissao = $tiposPermissao;
    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var PayloadJWT */
        $payloadJWT = $request->getAttribute('payloadJWT');
        $papel = $payloadJWT->role();

        $tipoPermissao = $this->obterTipoPermissaoParaSerExecutado($papel);
        if (! $tipoPermissao instanceof TipoPermissao) {
            return $this->semPermissao();
        }

        if (! $this->existeMiddleware($tipoPermissao->middleware)) {
            return $this->semPermissao();
        }

        $resultado = call_user_func_array([ MiddlewareFactory::class, $tipoPermissao->middleware ], $tipoPermissao->parametrosMiddleware);

        if (! $this->chamadaMiddlewareEhValida($resultado)) {
            return $this->semPermissao('a');
        }

        return $resultado($request, $handler);
    }

    private function existeMiddleware($middleware)
    {
        return method_exists(MiddlewareFactory::class, $middleware);
    }

    private function chamadaMiddlewareEhValida($resultado)
    {
        return is_callable($resultado);
    }

    private function obterTipoPermissaoParaSerExecutado(string $papel)
    {
        $tipo = null;

        if (! empty($this->tiposPermissao)) {
            foreach ($this->tiposPermissao as $tipoPermissao) {
                if ($tipoPermissao instanceof TipoPermissao && $tipoPermissao->tipo == $papel) {
                    $tipo = $tipoPermissao;
                    break;
                }
            }
        }

        return $tipo;
    }

    private function semPermissao(string $mensagem = 'Você não tem permissão para realizar essa ação.')
    {
        return RespostaHttp::enviarResposta(new Response(), HttpStatusCode::FORBIDDEN, [
            'message' => $mensagem
        ]);
    }
}
