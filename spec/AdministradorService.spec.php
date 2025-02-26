<?php

use Mockery;
use app\databases\DAOEmBDR;
use app\exceptions\ServiceException;
use app\classes\Administrador;
use app\services\AdministradorService;

describe( 'AdministradorService', function () {
    beforeEach(function () {
        $this->dao = Mockery::mock( DAOEmBDR::class );
        $this->service = new AdministradorService( $this->dao );
    });

    describe( 'Salvar', function(){
        function validarErroSalvar( $e, $campo, $mensagemEsperada ){
            $erro = json_decode( $e->getMessage(), true );
            expect($erro)->not->toBeEmpty();
            expect($erro)->toHaveLength(1);
            expect($erro)->toContainKey($campo);
            expect($erro[$campo])->toEqual($mensagemEsperada);
        }

        it('Lança exceção ao enviar nome vazio para administrador', function() {
            $this->dao->shouldReceive('existe')->andReturn( false );

            $administrador = new Administrador( 0, '', 'arturalvesdeoliveira28@gmail.com', 12345678 );

            try {
                $this->service->salvar( $administrador );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'nome', 'Preencha o nome.' );
            }
        });

        it('Lança exceção ao enviar nome com tamanho maior que o permitido para administrador', function() {
            $this->dao->shouldReceive('existe')->andReturn( false );

            $nomeForaDoTamanhoPermitido = str_repeat('a', AdministradorService::TAMANHO_MAXIMO_NOME + 1);
            $administrador = new Administrador( 0, $nomeForaDoTamanhoPermitido, 'arturalvesdeoliveira28@gmail.com', 12345678 );

            try {
                $this->service->salvar( $administrador );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'nome', 'O nome deve ter entre ' . AdministradorService::TAMANHO_MINIMO_NOME . ' e ' . AdministradorService::TAMANHO_MAXIMO_NOME . ' caracteres.' );
            }
        });

        it('Lança exceção ao enviar email vazio para administrador', function() {
            $this->dao->shouldReceive('existe')->andReturn( false );

            $administrador = new Administrador( 0, 'Artur Alves', '', 12345678 );

            try {
                $this->service->salvar( $administrador );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'email', 'Preencha o email.' );
            }
        });

        it('Lança exceção ao enviar email com tamanho maior que o permitido para administrador', function() {
            $this->dao->shouldReceive('existe')->andReturn( false );

            $emailForaDoTamanhoPermitido = str_repeat('a', AdministradorService::TAMANHO_MAXIMO_EMAIL + 1);
            $administrador = new Administrador( 0, 'Artur Alves', $emailForaDoTamanhoPermitido, 12345678 );

            try {
                $this->service->salvar( $administrador );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'email', 'O email deve ter entre ' . AdministradorService::TAMANHO_MINIMO_EMAIL . ' e ' . AdministradorService::TAMANHO_MAXIMO_EMAIL . ' caracteres.' );
            }
        });

        it('Lança exceção ao enviar email inválido para administrador', function() {
            $this->dao->shouldReceive('existe')->andReturn( false );

            $administrador = new Administrador( 0, 'Artur Alves', 'aaaa', 12345678 );

            try {
                $this->service->salvar( $administrador );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'email', 'Email inválido.' );
            }
        });

        it('Lança exceção ao enviar email que pertence a outro administrador', function() {
            $this->dao->shouldReceive('existe')->andReturn( true );

            $administrador = new Administrador( 0, 'Artur Alves', 'arturalvesdeoliveira28@gmail.com', 12345678 );

            try {
                $this->service->salvar( $administrador );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'email', 'Email já pertence a um administrador.' );
            }
        });

        it('Lança exceção ao enviar senha vazia para administrador', function() {
            $this->dao->shouldReceive('existe')->andReturn( false );

            $administrador = new Administrador( 0, 'Artur Alves', 'arturalvesdeoliveira28@gmail.com', '' );

            try {
                $this->service->salvar( $administrador );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'senha', 'Preencha a senha.' );
            }
        });

        it('Lança exceção ao enviar senha com tamanho diferente que o permitido para administrador', function() {
            $this->dao->shouldReceive('existe')->andReturn( false );

            $senhaForaDoTamanhoPermitido = str_repeat('a', AdministradorService::TAMANHO_SENHA + 1);
            $administrador = new Administrador( 0, 'Artur Alves', 'arturalvesdeoliveira28@gmail.com', $senhaForaDoTamanhoPermitido );

            try {
                $this->service->salvar( $administrador );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'senha', 'A senha deve ter ' . AdministradorService::TAMANHO_SENHA . ' caracteres.' );
            }
        });
    } );
});
