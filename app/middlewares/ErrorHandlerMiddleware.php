<?php

namespace app\middlewares;

use app\core\HttpStatusCode;
use app\core\RespostaHttp;
use Slim\Exception\HttpException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpUnauthorizedException;
use Throwable;

class ErrorHandlerMiddleware {
    public function __invoke( Request $request, Throwable $e, bool $displayErrorDetails ){
        $status = $e instanceof HttpException ? $e->getCode() : HttpStatusCode::INTERNAL_SERVER_ERROR;
        $mensagem = $this->obterMensagemErroDeAcordoComExceptionSlim($e);

        return RespostaHttp::enviarResposta( new Response(), $status, [
            'erro' => $mensagem . $e->getMessage()
        ] );
    }

    private function obterMensagemErroDeAcordoComExceptionSlim( Throwable $e ){
        $mensagem = 'Houve um erro interno.';

        if( $e instanceof HttpNotFoundException ){
            $mensagem = 'Rota não encontrada.';
        } elseif ( $e instanceof HttpMethodNotAllowedException ){
            $mensagem = 'Método não suportado.';
        } elseif ( $e instanceof HttpUnauthorizedException ){
            $mensagem = 'Não autorizado.';
        } elseif ( $e instanceof HttpForbiddenException ){
            $mensagem = 'Acesso proibido.';
        } elseif ( $e instanceof HttpBadRequestException ){
            $mensagem = 'Requisição inválida.';
        }

        return $mensagem;
    }
}
