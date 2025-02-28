<?php

namespace app\middlewares;

use Slim\Psr7\Response;
use app\classes\jwt\PayloadJWT;
use app\classes\http\RespostaHttp;
use app\classes\http\HttpStatusCode;
use app\traits\Autenticavel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AutenticacaoMiddleware {
    use Autenticavel;

    public function __invoke( ServerRequestInterface $request, RequestHandlerInterface $handler ): ResponseInterface {
        $autorization = $request->getHeaderLine('Authorization');
        if( ! $autorization || ! preg_match( '/^Bearer\s(\S+)/', $autorization, $matches ) ){
            return $this->administradorNaoAutenticado( 'Token de autenticação não foi enviado.' );
        }

        $token = $matches[1];

        $payloadJWT = $this->decodificarToken( $token );

        if( ! $payloadJWT instanceof PayloadJWT ){
            return $this->administradorNaoAutenticado();
        }

        $request = $request->withAttribute( 'payloadJWT', $payloadJWT );

        return $handler->handle( $request );
    }

    private function administradorNaoAutenticado( string $mensagem = 'Token de autenticação inválido.' ){
        return RespostaHttp::enviarResposta( new Response(), HttpStatusCode::UNAUTHORIZED, [
            'erro' => $mensagem
        ] );
    }
}
