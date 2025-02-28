<?php
namespace app\middlewares;

use app\classes\Administrador;
use app\classes\factory\ClassFactory;
use Slim\Psr7\Response;
use app\classes\jwt\PayloadJWT;
use app\classes\http\RespostaHttp;
use app\classes\http\HttpStatusCode;
use app\services\AdministradorService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PermissaoAdministradorMiddleware {
    private $permissoesNecessarias;

    public function __construct( array $permissoesNecessarias ){
        $this->permissoesNecessarias = $permissoesNecessarias;
    }

    public function __invoke( ServerRequestInterface $request, RequestHandlerInterface $handler ): ResponseInterface {
        /** @var PayloadJWT */
        $payloadJWT = $request->getAttribute('payloadJWT');

        /** @var AdministradorService */
        $administradorService = ClassFactory::makeService( Administrador::class );
        $administrador = $administradorService->obterComId( $payloadJWT->sub() );
        if( ! $administrador instanceof Administrador ){
            return $this->administradorSemPermissao();
        }

        foreach( $this->permissoesNecessarias as $permissao ){
            if( ! $administrador->possuiPermissao( $permissao ) ){
                return $this->administradorSemPermissao();
            }
        }

        return $handler->handle( $request );
    }

    private function administradorSemPermissao( string $mensagem = 'Você não tem permissão para realizar essa ação.' ){
        return RespostaHttp::enviarResposta( new Response(), HttpStatusCode::FORBIDDEN, [
            'erro' => $mensagem
        ] );
    }
}
