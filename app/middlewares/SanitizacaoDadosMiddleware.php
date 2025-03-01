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
        array_walk_recursive( $array, function( &$value ){
            $value = htmlspecialchars( strip_tags( trim( $value ) ) );
        });
    }
}