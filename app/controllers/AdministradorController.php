<?php

namespace app\controllers;

use app\classes\Administrador;
use app\controllers\Controller;
use app\classes\http\HttpStatusCode;
use app\classes\jwt\TokenJWT;
use app\services\AdministradorService;

class AdministradorController extends Controller
{
    protected function criar(array $dados)
    {
        $administrador = new Administrador();
        $camposSimples = [ 'id', 'nome', 'email', 'senha' ];
        $this->povoarSimples($administrador, $camposSimples, $dados);

        return $administrador;
    }

    public function login(array $dados)
    {
        [ 'email' => $email, 'senha' => $senha ] = $dados;

        /** @var AdministradorService */
        $administradorService = $this->getService();
        /** @var TokenJWT */
        $tokenJWT = $administradorService->autenticar($email, $senha);

        return $this->resposta(HttpStatusCode::OK, [
            'message' => 'Administrador autenticado com sucesso.',
            'data' => [
                'Token' => $tokenJWT->codigo(),
                'Duração' => $tokenJWT->validadeTokenFormatada()
            ]
        ]);
    }

    public function salvarPermissoes(array $dados, $args)
    {
        $id = intval($args['id']);
        $permissoes = $dados['permissoes'] ?? [];

        /** @var AdministradorService */
        $administradorService = $this->getService();
        $administradorService->salvarPermissoes($permissoes, $id);

        return $this->resposta(HttpStatusCode::OK, [
            'message' => 'Permissões salvas com sucesso.'
        ]);
    }
}
