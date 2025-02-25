<?php

namespace app\classes;

class Categoria extends Model {
    private int $id = 0;
    private string $nome = '';
    private string $descricao = '';

    const TAMANHO_MINIMO_NOME = 1;
    const TAMANHO_MAXIMO_NOME = 100;
    const TAMANHO_MINIMO_DESCRICAO = 1;
    const TAMANHO_MAXIMO_DESCRICAO = 500;

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

    public function getDescricao(){
        return $this->descricao;
    }

    public function setDescricao( string $descricao ){
        $this->descricao = $descricao;
    }
}