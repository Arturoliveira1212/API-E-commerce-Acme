<?php

namespace app\classes;

class Endereco extends Model
{
    private int $id = 0;
    private string $logradouro = '';
    private string $cidade = ''; // TO DO => Tratar cidade como objeto.
    private string $bairro = ''; // TO DO => Tratar bairro como objeto.
    private string $numero = 'SN';
    private string $cep = '';
    private string $complemento = '';

    public function __construct(
        int $id = 0,
        string $logradouro = '',
        string $cidade = '',
        string $bairro = '',
        string $numero = 'SN',
        string $cep = '',
        string $complemento = ''
    ) {
        $this->setId($id);
        $this->setLogradouro($logradouro);
        $this->setCidade($cidade);
        $this->setBairro($bairro);
        $this->setNumero($numero);
        $this->setCep($cep);
        $this->setComplemento($complemento);
    }


    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getLogradouro()
    {
        return $this->logradouro;
    }

    public function setLogradouro(string $logradouro)
    {
        $this->logradouro = $logradouro;
    }

    public function getCidade()
    {
        return $this->cidade;
    }

    public function setCidade(string $cidade)
    {
        $this->cidade = $cidade;
    }

    public function getBairro()
    {
        return $this->bairro;
    }

    public function setBairro(string $bairro)
    {
        $this->bairro = $bairro;
    }

    public function getNumero()
    {
        return $this->numero;
    }

    public function setNumero(string $numero)
    {
        $this->numero = $numero;
    }

    public function getCep()
    {
        return $this->cep;
    }

    public function setCep(string $cep)
    {
        $this->cep = $cep;
    }

    public function getComplemento()
    {
        return $this->complemento;
    }

    public function setComplemento(string $complemento)
    {
        $this->complemento = $complemento;
    }

    public function emArray(): array
    {
        return [
            'id' => $this->getId(),
            'logradouro' => $this->getLogradouro(),
            'cidade' => $this->getCidade(),
            'bairro' => $this->getBairro(),
            'numero' => $this->getNumero(),
            'cep' => $this->getCep(),
            'complemento' => $this->getComplemento()
        ];
    }
}
