<?php

namespace app\classes;

use Throwable;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use app\classes\http\RespostaHttp;
use app\classes\http\HttpStatusCode;
use app\exceptions\ServiceException;
use app\classes\factory\ClassFactory;
use app\exceptions\NaoAutorizadoException;
use app\exceptions\NaoEncontradoException;

abstract class GerenciadorRecurso {
    public static function executar( string $controller, string $metodo, Request $request, Response $response, $args ){
        try {
            $corpoRequisicao = self::limparArray( (array) $request->getParsedBody() );
            $parametros = self::limparArray( (array) $request->getQueryParams() );
            $payloadJWT = $request->getAttribute('payloadJWT');

            $controller = ClassFactory::makeController( $controller );
            $retorno = $controller->$metodo( $corpoRequisicao, $args, $parametros, $payloadJWT );
            $resposta = RespostaHttp::enviarResposta( $response, $retorno['status'] ?? HttpStatusCode::OK, $retorno['data'] ?? [] );
        } catch( NaoEncontradoException $e ){
            $resposta = RespostaHttp::enviarResposta( $response, HttpStatusCode::NOT_FOUND, [
                'message' => $e->getMessage()
            ] );
        } catch( ServiceException $e ){
            $resposta = RespostaHttp::enviarResposta( $response, HttpStatusCode::BAD_REQUEST, [
                'message' => 'Os dados enviados são inválidos.',
                'data' => [
                    'erros' => json_decode( $e->getMessage(), true )
                ]
            ] );
        } catch( NaoAutorizadoException $e ){
            $resposta = RespostaHttp::enviarResposta( $response, HttpStatusCode::UNAUTHORIZED, [
                'message' => $e->getMessage()
            ] );
        } catch( Throwable $e ){
            $resposta = RespostaHttp::enviarResposta( $response, HttpStatusCode::INTERNAL_SERVER_ERROR, [
                'message' => 'Houve um erro interno.' . $e
            ] );
        } finally {
            return $resposta;
        }
    }

    private static function limparArray( array $array ){
        $arrayLimpo = [];

        foreach( $array as $chave => $valor ){
            $arrayLimpo[ $chave ] = htmlspecialchars( strip_tags( trim( $valor ) ) );
        }

        return $arrayLimpo;
    }
}