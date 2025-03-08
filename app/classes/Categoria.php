<?php

namespace app\classes;

class Categoria extends Model {
    private int $id = 0;
    private string $nome = '';
    private string $descricao = '';

    public function __construct(
        int $id = 0,
        string $nome = '',
        string $descricao = ''
    ){
        $this->setId( $id );
        $this->setNome( $nome );
        $this->setDescricao( $descricao );
    }

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

    public function emArray() :array {
        return [
            'id' => $this->getId(),
            'nome' => $this->getNome(),
            'descricao' => $this->getDescricao()
        ];
    }
}