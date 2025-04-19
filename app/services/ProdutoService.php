<?php

namespace app\services;

use app\classes\Produto;
use app\services\Service;
use app\classes\Categoria;
use app\classes\utils\Validador;
use app\classes\enum\OperacaoObjeto;
use app\exceptions\NaoEncontradoException;

class ProdutoService extends Service
{
    public const TAMANHO_MINIMO_NOME = 2;
    public const TAMANHO_MAXIMO_NOME = 100;
    public const TAMANHO_REFERENCIA = 10;
    public const TAMANHO_MINIMO_COR = 3;
    public const TAMANHO_MAXIMO_COR = 50;
    public const PRECO_MINIMO = 1;
    public const PRECO_MAXIMO = 10000;
    public const TAMANHO_MINIMO_DESCRICAO = 3;
    public const TAMANHO_MAXIMO_DESCRICAO = 300;

    protected function validar($produto, int $operacaoObjeto, array &$erro = [])
    {
        $this->validarNome($produto, $operacaoObjeto, $erro);
        $this->validarReferencia($produto, $operacaoObjeto, $erro);
        $this->validarCor($produto, $erro);
        $this->validarPreco($produto, $erro);
        $this->validarDescricao($produto, $erro);
        $this->validarCategoria($produto, $erro);
    }

    private function validarNome(Produto $produto, int $operacaoObjeto, array &$erro)
    {
        $validacaoTamanhoNome = Validador::validarTamanhoTexto($produto->getNome(), self::TAMANHO_MINIMO_NOME, self::TAMANHO_MAXIMO_NOME);
        if ($validacaoTamanhoNome == 0) {
            $erro['nome'] = 'Preencha o nome.';
        } elseif ($validacaoTamanhoNome == -1) {
            $erro['nome'] = 'O nome deve ter entre ' . self::TAMANHO_MINIMO_NOME . ' e ' . self::TAMANHO_MAXIMO_NOME . ' caracteres.';
        } elseif ($this->nomeJaCadastrado($produto, $operacaoObjeto)) {
            $erro['nome'] = 'Produto já cadastrado com esse nome.';
        }
    }

    private function nomeJaCadastrado(Produto $produto, int $operacaoObjeto)
    {
        try {
            $produtoCadastrado = $this->obterComNome($produto->getNome());

            if ($operacaoObjeto == OperacaoObjeto::CADASTRAR) {
                return true;
            }

            if ($operacaoObjeto == OperacaoObjeto::EDITAR && $produto->getId() != $produtoCadastrado->getId()) {
                return true;
            }

            return false;
        } catch (NaoEncontradoException $e) {
            return false;
        }
    }

    private function validarReferencia(Produto $produto, int $operacaoObjeto, array &$erro)
    {
        $validacaoTamanhoReferencia = Validador::validarTamanhoTexto($produto->getReferencia(), self::TAMANHO_REFERENCIA, self::TAMANHO_REFERENCIA);
        if ($validacaoTamanhoReferencia == 0) {
            $erro['referencia'] = 'Preencha a referência.';
        } elseif ($validacaoTamanhoReferencia == -1) {
            $erro['referencia'] = 'A referência deve ter ' . self::TAMANHO_REFERENCIA . ' caracteres.';
        } elseif ($this->referenciaJaCadastrada($produto, $operacaoObjeto)) {
            $erro['referencia'] = 'Produto já cadastrado com essa referência.';
        }
    }

    private function referenciaJaCadastrada(Produto $produto, int $operacaoObjeto)
    {
        try {
            $produtoCadastrado = $this->obterComReferencia($produto->getReferencia());

            if ($operacaoObjeto == OperacaoObjeto::CADASTRAR) {
                return true;
            }

            if ($operacaoObjeto == OperacaoObjeto::EDITAR && $produto->getId() != $produtoCadastrado->getId()) {
                return true;
            }

            return false;
        } catch (NaoEncontradoException $e) {
            return false;
        }
    }

    private function validarCor(Produto $produto, array &$erro)
    {
        $validacaoTamanhoCor = Validador::validarTamanhoTexto($produto->getCor(), self::TAMANHO_MINIMO_COR, self::TAMANHO_MAXIMO_COR);
        if ($validacaoTamanhoCor == 0) {
            $erro['cor'] = 'Preencha a cor.';
        } elseif ($validacaoTamanhoCor == -1) {
            $erro['cor'] = 'A cor deve ter entre ' . self::TAMANHO_MINIMO_COR . ' e ' . self::TAMANHO_MAXIMO_COR . ' caracteres.';
        }
    }

    private function validarPreco(Produto $produto, array &$erro)
    {
        if ($produto->getPreco() < self::PRECO_MINIMO || $produto->getPreco() > self::PRECO_MAXIMO) {
            $erro['preco'] = 'O preço deve estar entre R$ ' . self::PRECO_MINIMO . ' e R$ ' . self::PRECO_MAXIMO . '.';
        }
    }

    private function validarDescricao(Produto $produto, array &$erro)
    {
        $validacaoTamanhoDescricao = Validador::validarTamanhoTexto($produto->getDescricao(), self::TAMANHO_MINIMO_DESCRICAO, self::TAMANHO_MAXIMO_DESCRICAO);
        if ($validacaoTamanhoDescricao == 0) {
            $erro['descricao'] = 'Preencha a descrição.';
        } elseif ($validacaoTamanhoDescricao == -1) {
            $erro['descricao'] = 'A descrição deve ter entre ' . self::TAMANHO_MINIMO_DESCRICAO . ' e ' . self::TAMANHO_MAXIMO_DESCRICAO . ' caracteres.';
        }
    }

    private function validarCategoria(Produto $produto, array &$erro)
    {
        if (! $produto->getCategoria() instanceof Categoria) {
            $erro['categoria'] = 'Categoria não encontrada.';
        }
    }

    /**
     * Método responsável por obter o produto pelo nome.
     *
     * @param string $nome
     * @return Produto
     * @throws NaoEncontradoException
     */
    public function obterComNome(string $nome)
    {
        $restricoes = [ 'nome' => $nome ];
        $produtos = (array) $this->obterComRestricoes($restricoes);

        $produto = array_shift($produtos);
        if (! $produto instanceof Produto) {
            throw new NaoEncontradoException('Recurso não encontrado.');
        }

        return $produto;
    }

    /**
     * Método responsável por obter o produto pela referência.
     *
     * @param string $referencia
     * @return Produto
     * @throws NaoEncontradoException
     */
    public function obterComReferencia(string $referencia)
    {
        $restricoes = [ 'referencia' => $referencia ];
        $produtos = (array) $this->obterComRestricoes($restricoes);

        $produto = array_shift($produtos);
        if (! $produto instanceof Produto) {
            throw new NaoEncontradoException('Recurso não encontrado.');
        }

        return $produto;
    }
}
