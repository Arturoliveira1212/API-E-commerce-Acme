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
        $corpoRequisicao = (array) $request->getParsedBody();

        if( ! $this->validarFormato( $contentType ) || empty( $corpoRequisicao ) ){
            return $this->corpoRequisicaoInvalido( [
                'erro' => 'O corpo da requisição deve ser em JSON válido.'
            ] );
        }

        $erros = $this->validarCampos( $corpoRequisicao );
        if( ! empty( $erros ) ){
            return $this->corpoRequisicaoInvalido( [
                'erros' => $erros
            ] );
        }

        return $handler->handle( $request );
    }

    private function corpoRequisicaoInvalido( array $data ){
        return RespostaHttp::enviarResposta( new Response(), HttpStatusCode::BAD_REQUEST, $data );
    }

    private function validarFormato( string $contentType ){
        return strpos( $contentType, $this->formato ) !== false;
    }

    private function validarCampos( array $corpoRequisicao ){
        $erros = [];

        foreach( $this->campos as $campo => $tipo ){
            if( ! isset( $corpoRequisicao[ $campo ] ) ){
                $erros[ $campo ] = "Campo {$campo} não foi enviado.";
            } else if( ! $this->tipoValido( $corpoRequisicao[ $campo ], $tipo ) ){
                $erros[ $campo ] = "Campo {$campo} deve ser {$tipo}";
            }
        }

        return $erros;
    }

    private function tipoValido( $valor, $tipo ){
        switch( $tipo ){
            case 'string':
                return is_string( $valor );
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
