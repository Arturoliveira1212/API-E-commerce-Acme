<?php

namespace app\classes;

use DateTime;

class Produto extends Model
{
    private int $id = 0;
    private string $nome = '';
    private string $referencia = '';
    private string $cor = ''; // TO DO => Tratar cor como objeto.
    private float $preco = 0.0;
    private string $descricao = '';
    private ?Categoria $categoria = null;
    private ?DateTime $dataCadastro = null;
    private array $itens = [];

    public function __construct(
        int $id = 0,
        string $nome = '',
        string $referencia = '',
        string $cor = '',
        float $preco = 0.0,
        string $descricao = '',
        ?Categoria $categoria = null
    ) {
        $this->setId($id);
        $this->setNome($nome);
        $this->setReferencia($referencia);
        $this->setCor($cor);
        $this->setPreco($preco);
        $this->setDescricao($descricao);
        $this->setCategoria($categoria);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome(string $nome)
    {
        $this->nome = $nome;
    }

    public function getReferencia()
    {
        return $this->referencia;
    }

    public function setReferencia(string $referencia)
    {
        $this->referencia = $referencia;
    }

    public function getCor()
    {
        return $this->cor;
    }

    public function setCor(string $cor)
    {
        $this->cor = $cor;
    }

    public function getPreco()
    {
        return $this->preco;
    }

    public function setPreco(float $preco)
    {
        $this->preco = $preco;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao)
    {
        $this->descricao = $descricao;
    }

    public function getCategoria()
    {
        return $this->categoria;
    }

    public function setCategoria(?Categoria $categoria)
    {
        $this->categoria = $categoria;
    }

    public function getDataCadastro()
    {
        if (! empty($formato) && $this->dataCadastro instanceof DateTime) {
            return $this->dataCadastro->format($formato);
        }
        return $this->dataCadastro;
    }

    public function setDataCadastro(?DateTime $dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
    }

    public function getItens()
    {
        return $this->itens;
    }

    public function setItens(array $itens)
    {
        $this->itens = $itens;
    }

    public function emArray(): array
    {
        return [
            'id' => $this->getId(),
            'nome' => $this->getNome(),
            'referencia' => $this->getReferencia(),
            'cor' => $this->getCor(),
            'preco' => $this->getPreco(),
            'descricao' => $this->getDescricao(),
            'categoria' => $this->getCategoria() instanceof Categoria ? $this->getCategoria()->emArray() : null,
            'dataCadastro' => $this->getDataCadastro()->format('Y-m-d H:i:s'),
            'itens' => $this->getItens()
        ];
    }
}
