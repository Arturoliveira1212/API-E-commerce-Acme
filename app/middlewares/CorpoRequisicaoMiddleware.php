<?php

namespace app\middlewares;

use Slim\Psr7\Response;
use app\classes\http\RespostaHttp;
use app\classes\http\HttpStatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorpoRequisicaoMiddleware {
    private string $formato;
    private array $campos;

    public function __construct( string $formato, array $campos ){
        $this->formato = $formato;
        $this->campos = $campos;
    }

    public function __invoke( ServerRequestInterface $request, RequestHandlerInterface $handler ): ResponseInterface {
        $contentType = $request->getHeaderLine('Content-Type');
        $corpoRequisicao = $request->getParsedBody();

        if( empty( $corpoRequisicao ) ||  ! $this->validarFormato( $contentType ) ){
            return $this->corpoRequisicaoInvalido( [
                'message' => 'O corpo da requisição deve ser em JSON válido.'
            ] );
        }

        $erros = [];
        $corpoRequisicaoValidado = $this->validarCampos( $corpoRequisicao, $erros );
        if( ! empty( $erros ) ){
            return $this->corpoRequisicaoInvalido( [
                'message' => 'O corpo da requisição é inválido.',
                'data' => [
                    'erros' => $erros
                ]
            ] );
        }

        $request = $request->withParsedBody( $corpoRequisicaoValidado );

        return $handler->handle( $request );
    }

    private function corpoRequisicaoInvalido( array $data ){
        return RespostaHttp::enviarResposta( new Response(), HttpStatusCode::BAD_REQUEST, $data );
    }

    private function validarFormato( string $contentType ){
        return strpos( $contentType, $this->formato ) !== false;
    }

    private function validarCampos( array $corpoRequisicao, array &$erros = [] ){
        $corpoRequisicaoValidado = [];

        foreach( $this->campos as $campo => $tipo ){
            if( ! isset( $corpoRequisicao[ $campo ] ) ){
                $erros[ $campo ] = "Campo {$campo} não foi enviado.";
            } else if( ! $this->tipoValido( $corpoRequisicao[ $campo ], $tipo ) ){
                $erros[ $campo ] = "Campo {$campo} deve ser do tipo {$tipo}.";
            } else {
                $corpoRequisicaoValidado[ $campo ] = $corpoRequisicao[ $campo ];
            }
        }

        return $corpoRequisicaoValidado;
    }

    private function tipoValido( $valor, $tipo ){
        switch( $tipo ){
            case 'string':
                return is_string( $valor );
            case 'numeric':
                return is_numeric( $valor );
            case 'int':
                return is_int( $valor );
            case 'float':
                return is_float( $valor );
            case 'bool':
                return is_bool( $valor );
            case 'array':
                return is_array( $valor );
            case 'object':
                return is_object( $valor );
            default:
                return false;
        }
    }
}
