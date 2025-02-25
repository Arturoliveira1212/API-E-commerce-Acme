<?php

namespace app\classes;

class Administrador extends Model {
    private int $id = 0;
    private string $nome = '';
    private string $email = '';
    private string $senha = '';
    private array $direitos = [];

    const TAMANHO_MINIMO_NOME = 1;
    const TAMANHO_MAXIMO_NOME = 100;

    public function getId(){
        return $this->id;
    }

    public function setId( int $id ){
        $this->id = $id;
    }

    public function getNome(){
        return $this->nome;
    }

    public function setNome( string $nome ){
        $this->nome = $nome;
    }

    public function getEmail(){
        return $this->email;
    }

    public function setEmail( string $email ){
        $this->email = $email;
    }

    public function getSenha(){
        return $this->senha;
    }

    public function setSenha( string $senha ){
        $this->senha = $senha;
    }

    public function getDireitos(){
        return $this->direitos;
    }

    public function setDireitos( array $direitos ){
        $this->direitos = $direitos;
    }
}