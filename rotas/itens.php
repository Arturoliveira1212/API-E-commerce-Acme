<?php

use app\classes\Item;
use app\classes\TipoPermissao;
use app\classes\GerenciadorRecurso;
use app\classes\factory\MiddlewareFactory;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Routing\RouteCollectorProxy;

$corpoRequisicaoSalvarItem = [
    'sku' => 'string',
    'tamanho' => 'string',
    'estoque' => 'numeric',
    'pesoEmGramas' => 'numeric'
];

$app->group('/itens', function (RouteCollectorProxy $group) {
    $corpoRequisicaoEditarItem = [
        'sku' => 'string',
        'tamanho' => 'string',
        'pesoEmGramas' => 'numeric'
    ];

    $corpoRequisicaoMovimentarEstoqueItem = [
        'quantidade' => 'int',
        'operacao' => 'string'
    ];

    $group->put('/{id}', function (Request $request, Response $response, $args) {
        return GerenciadorRecurso::executar(Item::class, 'editar', $request, $response, $args);
    })
        ->add(MiddlewareFactory::corpoRequisicao($corpoRequisicaoEditarItem))
        ->add(MiddlewareFactory::permissao(new TipoPermissao('admin', 'permissaoAdministrador', [ 'Editar Item' ])))
        ->add(MiddlewareFactory::autenticacao());

    $group->delete('/{id}', function (Request $request, Response $response, $args) {
        return GerenciadorRecurso::executar(Item::class, 'excluirComId', $request, $response, $args);
    })
        ->add(MiddlewareFactory::permissao(new TipoPermissao('admin', 'permissaoAdministrador', [ 'Excluir Item' ])))
        ->add(MiddlewareFactory::autenticacao());

    $group->get('', function (Request $request, Response $response, $args) {
        return GerenciadorRecurso::executar(Item::class, 'obterTodos', $request, $response, $args);
    });

    $group->get('/{id}', function (Request $request, Response $response, $args) {
        return GerenciadorRecurso::executar(Item::class, 'obterComId', $request, $response, $args);
    });

    $group->patch('/{id}/estoque', function (Request $request, Response $response, $args) {
        return GerenciadorRecurso::executar(Item::class, 'movimentarEstoque', $request, $response, $args);
    })
        ->add(MiddlewareFactory::corpoRequisicao($corpoRequisicaoMovimentarEstoqueItem))
        ->add(MiddlewareFactory::permissao(new TipoPermissao('admin', 'permissaoAdministrador', [ 'Movimentar Estoque Item' ])))
        ->add(MiddlewareFactory::autenticacao());
});

$app->post('/produtos/{id}/itens', function (Request $request, Response $response, $args) {
    return GerenciadorRecurso::executar(Item::class, 'novo', $request, $response, $args);
})
    ->add(MiddlewareFactory::corpoRequisicao($corpoRequisicaoSalvarItem))
    ->add(MiddlewareFactory::permissao(new TipoPermissao('admin', 'permissaoAdministrador', [ 'Cadastrar Item' ])))
    ->add(MiddlewareFactory::autenticacao());

$app->get('/produtos/{id}/itens', function (Request $request, Response $response, $args) {
    return GerenciadorRecurso::executar(Item::class, 'obterItensDoProduto', $request, $response, $args);
});
