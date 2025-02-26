<?php

namespace app\middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SanitizacaoDadosMiddleware {
    public function __invoke( ServerRequestInterface $request, RequestHandlerInterface $handler ): ResponseInterface {
        $corpoRequisicao = $request->getParsedBody();
        if( is_array( $corpoRequisicao ) ){
            array_walk_recursive( $corpoRequisicao, function( &$value ){
                $value = htmlspecialchars( strip_tags( trim( $value ) ) );
            });

            $request = $request->withParsedBody( $corpoRequisicao );
        }

        $parametros = $request->getQueryParams();
        if( is_array( $parametros ) ){
            array_walk_recursive( $parametros, function( &$value ){
                $value = htmlspecialchars( strip_tags( trim( $value ) ) );
            });

            $request = $request->withParsedBody( $parametros );
        }

        return $handler->handle( $request );
    }
}