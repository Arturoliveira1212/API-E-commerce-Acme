<?php

namespace app\dao;

use app\classes\Item;
use app\dao\DAOEmBDR;
use app\classes\Produto;
use app\classes\Categoria;
use app\services\ItemService;
use app\classes\factory\ClassFactory;
use app\classes\utils\ConversorDados;

class ProdutoDAO extends DAOEmBDR
{
    protected function nomeTabela()
    {
        return 'produto';
    }

    protected function adicionarNovo($produto, ?int $idRecursoPai = null)
    {
        $comando = "INSERT INTO {$this->nomeTabela()} (id, nome, referencia, cor, preco, descricao, idCategoria, dataCadastro) VALUES (:id, :nome, :referencia, :cor, :preco, :descricao, :idCategoria, NOW())";
        $this->getBancoDados()->executar($comando, $this->parametros($produto));
    }

    protected function atualizar($produto)
    {
        $comando = "UPDATE {$this->nomeTabela()} SET nome = :nome, referencia = :referencia, cor = :cor, preco = :preco, descricao = :descricao, idCategoria = :idCategoria WHERE id = :id";
        $this->getBancoDados()->executar($comando, $this->parametros($produto));
    }

    protected function parametros($produto)
    {
        $produto = ConversorDados::converterEmArray($produto);
        unset($produto['dataCadastro']);

        return $produto;
    }

    protected function obterQuery(array $restricoes, array &$parametros)
    {
        $nomeTabela = $this->nomeTabela();

        $select = "SELECT * FROM {$nomeTabela}";
        $where = ' WHERE ativo = 1 ';
        $join = '';
        $orderBy = '';

        if (isset($restricoes['nome'])) {
            $where .= " AND {$nomeTabela}.nome = :nome ";
            $parametros['nome'] = $restricoes['nome'];
        }

        if (isset($restricoes['referencia'])) {
            $where .= " AND {$nomeTabela}.referencia = :referencia ";
            $parametros['referencia'] = $restricoes['referencia'];
        }

        $comando = $select . $join . $where . $orderBy;
        return $comando;
    }

    protected function transformarEmObjeto(array $linhas)
    {
        /** @var Produto */
        $produto = ConversorDados::converterEmObjeto(Produto::class, $linhas);
        $this->preencherCategoria($produto, intval($linhas['idCategoria']));
        $this->preencherItensDoProduto($produto);

        return $produto;
    }

    private function preencherCategoria(Produto $produto, int $idCategoria)
    {
        /** @var CategoriaDAO */
        $categoriaDAO = ClassFactory::makeDAO(Categoria::class);
        $categoria = $categoriaDAO->obterComId($idCategoria);
        $produto->setCategoria($categoria);
    }

    private function preencherItensDoProduto(Produto $produto)
    {
        /** @var ItemService */
        $itemService = ClassFactory::makeService(Item::class);
        $itensDoProduto = $itemService->obterItensDoProduto($produto->getId());
        if (! empty($itensDoProduto)) {
            $produto->setItens($itensDoProduto);
        }
    }
}
