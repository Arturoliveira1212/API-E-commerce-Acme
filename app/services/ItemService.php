<?php

namespace app\services;

use app\classes\enum\OperacaoEstoque;
use app\classes\enum\OperacaoObjeto;
use app\dao\ItemDAO;
use app\classes\Produto;
use app\services\Service;
use app\exceptions\ServiceException;
use app\classes\factory\ClassFactory;
use app\classes\Item;
use app\exceptions\NaoEncontradoException;
use Throwable;

class ItemService extends Service
{
    public const TAMANHO_SKU = 8;
    public const TAMANHOS_DISPONIVEIS = [
        'PP', 'P', 'M', 'G', 'GG', 'XG', 'U', '38', '39', '40', '41', '42'
    ];
    public const PESO_MINIMO = 1;
    public const PESO_MAXIMO = 1000000000;
    public const ESTOQUE_MINIMO = 0;
    public const ESTOQUE_MAXIMO = 10000000;
    public const QUANTIDADE_MINIMA = 1;
    public const QUANTIDADE_MAXIMA = 10000000;

    protected function preSalvar($item, int $operacaoObjeto, ?int $idRecursoPai = null)
    {
        if ($operacaoObjeto == OperacaoObjeto::CADASTRAR && ! $this->produtoDoItemExiste($idRecursoPai)) {
            throw new NaoEncontradoException('Recurso não encontrado.');
        }

        parent::preSalvar($item, $operacaoObjeto, $idRecursoPai);
    }

    protected function posSalvar($item, int $operacaoObjeto, ?int $idRecursoPai = null)
    {
        if ($operacaoObjeto == OperacaoObjeto::CADASTRAR) {
            $this->registrarMovimentacaoEstoque($item, $item->getEstoque());
        }

        parent::posSalvar($item, $operacaoObjeto, $idRecursoPai);
    }

    private function registrarMovimentacaoEstoque(Item $item, int $quantidade)
    {
        try {
            /** @var ItemDAO */
            $itemDAO = $this->getDao();
            $itemDAO->registrarMovimentacaoEstoque($item, $this->getPayloadJWT()->sub(), $quantidade, OperacaoEstoque::ADICIONAR);
        } catch (Throwable $th) {
        }
    }

    private function produtoDoItemExiste(?int $idProduto = null)
    {
        $produtoService = ClassFactory::makeService(Produto::class);
        $existe = $produtoService->existe('id', $idProduto);

        return $existe;
    }

    protected function validar($item, int $operacaoObjeto, array &$erro = [])
    {
        $this->validarSku($item, $operacaoObjeto, $erro);
        $this->validarTamanho($item, $erro);
        $this->validarPesoEmGramas($item, $erro);

        if ($operacaoObjeto == OperacaoObjeto::CADASTRAR) {
            $this->validarEstoque($item, $erro);
        }
    }

    private function validarSku(Item $item, int $operacaoObjeto, array &$erro)
    {
        if (empty($item->getSku())) {
            $erro['sku'] = 'Preencha o sku.';
        } elseif (! preg_match('/^[a-zA-Z0-9-]{8}$/', $item->getSku())) {
            $erro['sku'] = 'O sku deve ter ' . self::TAMANHO_SKU . ' caracteres, sendo eles números e letras.';
        } elseif ($this->skuJaCadastrado($item, $operacaoObjeto)) {
            $erro['sku'] = 'Item já cadastrado com esse sku.';
        }
    }

    private function skuJaCadastrado(Item $item, int $operacaoObjeto)
    {
        try {
            $itemCadastrado = $this->obterComSku($item->getSku());
            $existeItem = $itemCadastrado instanceof Item;

            if ($existeItem && $operacaoObjeto == OperacaoObjeto::CADASTRAR) {
                return true;
            }

            if ($existeItem &&  $operacaoObjeto == OperacaoObjeto::EDITAR && $item->getId() != $itemCadastrado->getId()) {
                return true;
            }

            return false;
        } catch (NaoEncontradoException $e) {
            return false;
        }
    }

    private function validarTamanho(Item $item, array &$erro)
    {
        if (empty($item->getTamanho())) {
            $erro['tamanho'] = 'Preencha o tamanho.';
        } elseif (! in_array($item->getTamanho(), self::TAMANHOS_DISPONIVEIS)) {
            $tamanhosDisponiveis = implode(',', self::TAMANHOS_DISPONIVEIS);
            $erro['tamanho'] = "Tamanho inválido. Os tamanhos disponíveis são: {$tamanhosDisponiveis}.";
        }
    }

    private function validarEstoque(Item $item, array &$erro)
    {
        if ($item->getEstoque() < self::ESTOQUE_MINIMO || $item->getEstoque() > self::ESTOQUE_MAXIMO) {
            $erro['estoque'] = 'O estoque deve estar entre ' . self::ESTOQUE_MINIMO . ' e ' . self::ESTOQUE_MAXIMO . ' unidades.';
        }
    }

    private function validarPesoEmGramas(Item $item, array &$erro)
    {
        if ($item->getPesoEmGramas() < self::PESO_MINIMO || $item->getPesoEmGramas() > self::PESO_MAXIMO) {
            $erro['pesoEmGramas'] = 'O peso deve estar entre ' . self::PESO_MINIMO . 'g e ' . self::PESO_MAXIMO . 'g.';
        }
    }

    public function movimentarEstoque(int $idItem, int $quantidade, int $operacaoEstoque)
    {
        $item = $this->obterComId($idItem);

        $erro = [];
        $this->validarMovimentacaoEstoque($item, $quantidade, $operacaoEstoque, $erro);
        if (! empty($erro)) {
            throw new ServiceException(json_encode($erro));
        }

        /** @var ItemDAO */
        $itemDAO = $this->getDao();
        $retorno = $itemDAO->atualizarEstoque($item, $quantidade, $operacaoEstoque);
        $this->registrarMovimentacaoEstoque($item, $quantidade);

        return $retorno;
    }

    private function validarMovimentacaoEstoque(Item $item, int $quantidade, int $operacaoEstoque, array &$erro = [])
    {
        $this->validarOperacaoEstoque($operacaoEstoque, $erro);
        $this->validarQuantidade($item, $quantidade, $operacaoEstoque, $erro);
    }

    private function validarOperacaoEstoque(int $operacaoEstoque, array &$erro)
    {
        if (! OperacaoEstoque::ehValido($operacaoEstoque)) {
            $erro['operacao'] = 'Operação inválida.';
        }
    }

    private function validarQuantidade(Item $item, int $quantidade, int $operacaoEstoque, array &$erro)
    {
        $estoqueAtual = $item->getEstoque();
        if ($quantidade < self::QUANTIDADE_MINIMA || $quantidade > self::QUANTIDADE_MAXIMA) {
            $erro['quantidade'] = 'A quantidade deve estar entre ' . self::QUANTIDADE_MINIMA . ' e ' . self::QUANTIDADE_MAXIMA . ' unidades.';
        } elseif ($operacaoEstoque == OperacaoEstoque::REMOVER && $quantidade > $estoqueAtual) {
            $erro['quantidade'] = 'O item não possui estoque disponível para a remoção.';
        }
    }

    public function obterItensDoProduto(int $idProduto)
    {
        if (! $this->produtoDoItemExiste($idProduto)) {
            throw new NaoEncontradoException('Recurso não encontrado.');
        }

        $restricoes = [ 'idProduto' => $idProduto ];
        $itens = $this->obterComRestricoes($restricoes);

        return $itens;
    }

    /**
     * Método responsável por obter o item pelo sku.
     *
     * @param string $sku
     * @return Item
     * @throws NaoEncontradoException
     */
    public function obterComSku(string $sku)
    {
        $restricoes = [ 'sku' => $sku ];
        $itens = (array) $this->obterComRestricoes($restricoes);

        $item = array_shift($itens);
        if (! $item instanceof Item) {
            throw new NaoEncontradoException('Recurso não encontrado.');
        }

        return $item;
    }
}
