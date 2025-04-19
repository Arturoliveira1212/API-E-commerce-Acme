<?php

namespace app\classes;

use app\classes\Model;
use app\dao\BancoDadosRelacional;

class Item extends Model
{
    private int $id = 0;
    private string $sku = '';
    private string $tamanho = ''; // TO DO => Tratar tamanho como objeto.
    private int $estoque = 0;
    private float $pesoEmGramas = 0.0;

    public function __construct(
        int $id = 0,
        string $sku = '',
        string $tamanho = '',
        int $estoque = 0,
        float $pesoEmGramas = 0.0
    ) {
        $this->setId($id);
        $this->setSku($sku);
        $this->setTamanho($tamanho);
        $this->setEstoque($estoque);
        $this->setPesoEmGramas($pesoEmGramas);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getSku()
    {
        return $this->sku;
    }

    public function setSku(string $sku)
    {
        $this->sku = $sku;
    }

    public function getTamanho()
    {
        return $this->tamanho;
    }

    public function setTamanho(string $tamanho)
    {
        $this->tamanho = $tamanho;
    }

    public function getEstoque()
    {
        return $this->estoque;
    }

    public function setEstoque(int $estoque)
    {
        $this->estoque = $estoque;
    }

    public function getPesoEmGramas()
    {
        return $this->pesoEmGramas;
    }

    public function setPesoEmGramas(float $pesoEmGramas)
    {
        $this->pesoEmGramas = $pesoEmGramas;
    }

    public function emArray(): array
    {
        return [
            'id' => $this->getId(),
            'sku' => $this->getSku(),
            'tamanho' => $this->getTamanho(),
            'estoque' => $this->getEstoque(),
            'pesoEmGramas' => $this->getPesoEmGramas()
        ];
    }
}
