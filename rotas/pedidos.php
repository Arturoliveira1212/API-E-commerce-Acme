<?php

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use app\classes\Produto;
use app\classes\TipoPermissao;
use app\classes\GerenciadorRecurso;
use Slim\Routing\RouteCollectorProxy;
use app\classes\factory\MiddlewareFactory;

$app->group('/pedidos', function (RouteCollectorProxy $group) {
    $corpoRequisicaoSalvarPedido = [
        'formaDePagamento' => 'int',
        'itensPedido' => 'array',
        'enderecoEntrega' => 'int',
        'valorFrete' => 'numeric'
    ];

    $group->post('', function (Request $request, Response $response, $args) {
        return GerenciadorRecurso::executar(Produto::class, 'novo', $request, $response, $args);
    })
        ->add(MiddlewareFactory::corpoRequisicao($corpoRequisicaoSalvarPedido))
        ->add(MiddlewareFactory::permissao(new TipoPermissao('cliente', 'permissaoPedido')))
        ->add(MiddlewareFactory::autenticacao());

    $group->get('', function (Request $request, Response $response, $args) {
        return GerenciadorRecurso::executar(Produto::class, 'obterTodos', $request, $response, $args);
    });

    $group->get('/{id}', function (Request $request, Response $response, $args) {
        return GerenciadorRecurso::executar(Produto::class, 'obterComId', $request, $response, $args);
    });
});

$app->get('/clientes/id/pedidos', function (Request $request, Response $response, $args) {
    return GerenciadorRecurso::executar(Produto::class, 'obterPedidosDoCliente', $request, $response, $args);
});
