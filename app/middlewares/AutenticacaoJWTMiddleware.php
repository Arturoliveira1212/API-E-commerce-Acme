<?php

namespace app\middlewares;

use Slim\Psr7\Response;
use app\classes\jwt\PayloadJWT;
use app\classes\http\RespostaHttp;
use app\classes\http\HttpStatusCode;
use app\classes\jwt\AutenticacaoJWT;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AutenticacaoJWTMiddleware {

    public function __invoke( ServerRequestInterface $request, RequestHandlerInterface $handler ): ResponseInterface {
        $autorization = $request->getHeaderLine('Authorization');
        if( ! $autorization || ! preg_match( '/Bearer\s(\S+)/', $autorization, $matches ) ){
            return RespostaHttp::enviarResposta( new Response(), HttpStatusCode::UNAUTHORIZED, [
                'erro' => 'Token de autenticação não foi enviado.'
            ] );
        }

        $token = $matches[1];

        $autenticacaoJWT = new AutenticacaoJWT();
        $payloadJWT = $autenticacaoJWT->decodificarToken( $token );

        if( ! $payloadJWT instanceof PayloadJWT ){
            return RespostaHttp::enviarResposta( new Response(), HttpStatusCode::UNAUTHORIZED, [
                'erro' => 'Token de autenticação inválido.'
            ] );
        }

        $request->withAttribute( 'payloadJWT', $payloadJWT );

        return $handler->handle( $request );
    }
}
