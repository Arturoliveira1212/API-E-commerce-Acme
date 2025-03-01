<?php

use app\databases\DAOEmBDR;
use app\exceptions\ServiceException;
use app\classes\Categoria;
use app\services\CategoriaService;

describe( 'CategoriaService', function () {
    beforeEach(function () {
        $this->dao = Mockery::mock( DAOEmBDR::class );
        $this->service = new CategoriaService( $this->dao );
    });

    describe( 'Salvar', function(){
        it( 'Lança exceção ao enviar nome vazio para categoria', function() {
            $this->dao->shouldReceive('existe')->andReturn( false );

            $categoria = new Categoria( 0, '', 'Descrição' );

            try {
                $this->service->salvar( $categoria );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'nome', 'Preencha o nome.' );
            }
        });

        it( 'Lança exceção ao enviar nome com tamanho maior que o permitido para categoria', function() {
            $nomeForaDoTamanhoPermitido = str_repeat('a', CategoriaService::TAMANHO_MAXIMO_NOME + 1);
            $categoria = new Categoria( 0, $nomeForaDoTamanhoPermitido, 'Descrição' );

            try {
                $this->service->salvar( $categoria );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'nome', 'O nome deve ter entre ' . CategoriaService::TAMANHO_MINIMO_NOME . ' e ' . CategoriaService::TAMANHO_MAXIMO_NOME . ' caracteres.' );
            }
        });

        it( 'Lança exceção ao enviar nome que pertence a uma categoria cadastrada', function() {
            $this->dao->shouldReceive('existe')->andReturn( true );

            $categoria = new Categoria( 0, 'Categoria', 'Descrição' );

            try {
                $this->service->salvar( $categoria );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'nome', 'Categoria já cadastrada com esse nome.' );
            }
        });

        it( 'Lança exceção ao enviar descrição vazia para categoria', function() {
            $this->dao->shouldReceive('existe')->andReturn( false );

            $categoria = new Categoria( 0, 'Nome', '' );

            try {
                $this->service->salvar( $categoria );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'descricao', 'Preencha a descrição.' );
            }
        });

        it( 'Lança exceção ao enviar descrição com tamanho maior que o permitido para categoria', function() {
            $this->dao->shouldReceive('existe')->andReturn( false );

            $descricaoForaDoTamanhoPermitido = str_repeat('a', CategoriaService::TAMANHO_MAXIMO_DESCRICAO + 1);
            $categoria = new Categoria( 0, 'Nome', $descricaoForaDoTamanhoPermitido );

            try {
                $this->service->salvar( $categoria );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'descricao', 'A descrição deve ter entre ' . CategoriaService::TAMANHO_MINIMO_DESCRICAO . ' e ' . CategoriaService::TAMANHO_MAXIMO_DESCRICAO . ' caracteres.' );
            }
        });
    });
});
