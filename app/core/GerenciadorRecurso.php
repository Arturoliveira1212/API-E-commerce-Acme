<?php

namespace app\core;

use app\exceptions\NaoAutorizadoException;
use app\exceptions\NaoEncontradoException;
use app\exceptions\ServiceException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Throwable;

abstract class GerenciadorRecurso {
    public static function executar( string $controller, string $metodo, Request $request, Response $response, $args ){
        try {
            $nomeController = substr( strrchr( $controller, '\\' ), 1 );
            $classe = str_replace( 'Controller', '', $nomeController );
            $controller = ClassFactory::makeController( $classe );

            $corpoRequisicao = (array) $request->getParsedBody();
            $parametros = (array) $request->getQueryParams();

            $retorno = $controller->$metodo( $corpoRequisicao, $args, $parametros );

            $resposta = RespostaHttp::enviarResposta( $response, $retorno['status'] ?? HttpStatusCode::OK, $retorno['data'] ?? [] );
        } catch( NaoEncontradoException $e ){
            $resposta = RespostaHttp::enviarResposta( $response, HttpStatusCode::NOT_FOUND, [
                'erro' => $e->getMessage()
            ] );
        } catch( ServiceException $e ){
            $resposta = RespostaHttp::enviarResposta( $response, HttpStatusCode::BAD_REQUEST, [
                'erros' => explode( '', $e->getMessage() )
            ] );
        } catch( NaoAutorizadoException $e ){
            $resposta = RespostaHttp::enviarResposta( $response, HttpStatusCode::UNAUTHORIZED, [
                'erro' => $e->getMessage()
            ] );
        } catch( Throwable $e ){
            $resposta = RespostaHttp::enviarResposta( $response, HttpStatusCode::INTERNAL_SERVER_ERROR, [
                'erro' => 'Houve um erro interno.' . $e->getMessage()
            ] );
        } finally {
            return $resposta;
        }
    }
}