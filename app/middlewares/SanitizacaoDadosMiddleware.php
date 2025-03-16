<?php

namespace app\middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SanitizacaoDadosMiddleware {
    public function __invoke( ServerRequestInterface $request, RequestHandlerInterface $handler ): ResponseInterface {
        $corpoRequisicao = $request->getParsedBody();
        if( is_array( $corpoRequisicao ) ){
            $this->limparArray( $corpoRequisicao );
            $request = $request->withParsedBody( $corpoRequisicao );
        }

        $parametros = $request->getQueryParams();
        if( is_array( $parametros ) ){
            $this->limparArray( $parametros );
            $request = $request->withQueryParams( $parametros );
        }

        return $handler->handle( $request );
    }

    private function limparArray( array &$array ){
        $novoArray = [];

        foreach( $array as $chave => $valor ){
            $this->limparValor( $chave );
            if ($chave === '') {
                continue;
            }

            if( is_array( $valor ) ){
                $this->limparArray( $valor );
            } else {
                $this->limparValor( $valor );
            }

            $novoArray[ $chave ] = $valor;
        }

        $array = $novoArray;
    }

    private function limparValor( &$valor ){
        $valor = htmlspecialchars( strip_tags( trim( $valor ) ) );
    }
}