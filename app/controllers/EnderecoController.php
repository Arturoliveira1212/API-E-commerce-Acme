<?php

namespace app\controllers;

use app\classes\Endereco;
use app\controllers\Controller;
use app\classes\http\HttpStatusCode;
use app\services\EnderecoService;

class EnderecoController extends Controller
{
    protected function criar(array $dados)
    {
        $endereco = new Endereco();
        $camposSimples = [ 'id', 'logradouro', 'cidade', 'bairro', 'numero', 'cep', 'complemento' ];
        $this->povoarSimples($endereco, $camposSimples, $dados);

        return $endereco;
    }

    public function obterEnderecosDoCliente(array $dados, $args)
    {
        $idCliente = isset($args['id']) ? intval($args['id']) : null;

        /** @var EnderecoService */
        $enderecoService = $this->getService();
        $enderecos = $enderecoService->obterEnderecosDoCliente($idCliente);

        return $this->resposta(HttpStatusCode::OK, [
            'message' => 'Sucesso ao obter os dados.',
            'data' => [
                $enderecos
            ]
        ]);
    }
}
