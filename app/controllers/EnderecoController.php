<?php

namespace app\controllers;

use app\classes\Endereco;
use app\controllers\Controller;
use app\classes\http\HttpStatusCode;

class EnderecoController extends Controller {
    protected function criar( array $dados ){
        $endereco = new Endereco();
        $camposSimples = [ 'id', 'logradouro', 'cidade', 'bairro', 'numero', 'cep', 'complemento' ];
        $this->povoarSimples( $endereco, $camposSimples, $dados );

        return new $endereco;
    }

    public function obterEnderecosDoCliente( array $dados, $args ){
        $restricoes = [ 'idCliente' => $args['idRecursoPai'] ];
        $enderecos = $this->getService()->obterComRestricoes( $restricoes );

        return $this->resposta( HttpStatusCode::OK, [
            'message' => 'Sucesso ao obter os dados.',
            'data' => [
                $enderecos
            ]
        ] );
    }
}