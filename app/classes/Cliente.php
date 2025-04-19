<?php

namespace app\classes;

use DateTime;

class Cliente extends Model
{
    private int $id = 0;
    private string $nome = '';
    private string $email = '';
    private string $cpf = '';
    private string $senha = '';
    private ?DateTime $dataNascimento = null;
    private array $enderecos = [];

    public function __construct(
        int $id = 0,
        string $nome = '',
        string $email = '',
        string $cpf = '',
        string $senha = '',
        ?DateTime $dataNascimento = null
    ) {
        $this->setId($id);
        $this->setNome($nome);
        $this->setEmail($email);
        $this->setCpf($cpf);
        $this->setSenha($senha);
        $this->setDataNascimento($dataNascimento);
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

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    public function getCpf()
    {
        return $this->cpf;
    }

    public function setCpf(string $cpf)
    {
        $this->cpf = $cpf;
    }

    public function getSenha()
    {
        return $this->senha;
    }

    public function setSenha(string $senha)
    {
        $this->senha = $senha;
    }

    public function getDataNascimento(string $formato = '')
    {
        if (! empty($formato) && $this->dataNascimento instanceof DateTime) {
            return $this->dataNascimento->format($formato);
        }
        return $this->dataNascimento;
    }

    public function setDataNascimento(?DateTime $dataNascimento)
    {
        $this->dataNascimento = $dataNascimento;
    }

    public function getEnderecos()
    {
        return $this->enderecos;
    }

    public function setEnderecos(array $enderecos)
    {
        $this->enderecos = $enderecos;
    }

    public function emArray(): array
    {
        return [
            'id' => $this->getId(),
            'nome' => $this->getNome(),
            'email' => $this->getEmail(),
            'cpf' => $this->getCpf(),
            'dataNascimento' => $this->getDataNascimento('d/m/Y'),
            'enderecos' => $this->getEnderecos()
        ];
    }
}
