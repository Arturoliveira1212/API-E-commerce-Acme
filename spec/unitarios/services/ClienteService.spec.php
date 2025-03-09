<?php

use app\dao\DAOEmBDR;
use app\exceptions\ServiceException;
use app\classes\Cliente;
use app\classes\jwt\TokenJWT;
use app\exceptions\NaoAutorizadoException;
use app\services\ClienteService;

describe( 'ClienteService', function () {
    beforeEach(function () {
        $this->dao = Mockery::mock( DAOEmBDR::class );
        $this->service = new ClienteService( $this->dao );
    });

    describe( 'Salvar', function(){
        it('Lança exceção ao enviar nome vazio para cliente', function() {
            allow( $this->service )->toReceive('obterComEmail')->andReturn( [] );
            allow( $this->service )->toReceive('obterComCpf')->andReturn( [] );

            $cliente = new Cliente( 0, '', 'arturalvesdeoliveira28@gmail.com', '775.832.150-05', 12345678, new DateTime( '2003-11-28' ) );

            try {
                $this->service->salvar( $cliente );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'nome', 'Preencha o nome.' );
            }
        });

        it('Lança exceção ao enviar nome com tamanho maior que o permitido para cliente', function() {
            allow( $this->service )->toReceive('obterComEmail')->andReturn( [] );
            allow( $this->service )->toReceive('obterComCpf')->andReturn( [] );

            $nomeForaDoTamanhoPermitido = str_repeat('a', ClienteService::TAMANHO_MAXIMO_NOME + 1);
            $cliente = new Cliente( 0, $nomeForaDoTamanhoPermitido, 'arturalvesdeoliveira28@gmail.com', '775.832.150-05', 12345678, new DateTime( '2003-11-28' ) );

            try {
                $this->service->salvar( $cliente );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'nome', 'O nome deve ter entre ' . ClienteService::TAMANHO_MINIMO_NOME . ' e ' . ClienteService::TAMANHO_MAXIMO_NOME . ' caracteres.' );
            }
        });

        it('Lança exceção ao enviar email vazio para cliente', function() {
            allow( $this->service )->toReceive('obterComEmail')->andReturn( [] );
            allow( $this->service )->toReceive('obterComCpf')->andReturn( [] );

            $cliente = new Cliente( 0, 'Artur Alves', '', '775.832.150-05', 12345678, new DateTime( '2003-11-28' ) );

            try {
                $this->service->salvar( $cliente );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'email', 'Preencha o email.' );
            }
        });

        it('Lança exceção ao enviar email com tamanho maior que o permitido para cliente', function() {
            allow( $this->service )->toReceive('obterComEmail')->andReturn( [] );
            allow( $this->service )->toReceive('obterComCpf')->andReturn( [] );

            $emailForaDoTamanhoPermitido = str_repeat('a', ClienteService::TAMANHO_MAXIMO_EMAIL + 1);
            $cliente = new Cliente( 0, 'Artur Alves', $emailForaDoTamanhoPermitido, '775.832.150-05', 12345678, new DateTime( '2003-11-28' ) );

            try {
                $this->service->salvar( $cliente );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'email', 'O email deve ter entre ' . ClienteService::TAMANHO_MINIMO_EMAIL . ' e ' . ClienteService::TAMANHO_MAXIMO_EMAIL . ' caracteres.' );
            }
        });

        it('Lança exceção ao enviar email inválido para cliente', function() {
            allow( $this->service )->toReceive('obterComEmail')->andReturn( [] );
            allow( $this->service )->toReceive('obterComCpf')->andReturn( [] );

            $cliente = new Cliente( 0, 'Artur Alves', 'aaa', '775.832.150-05', 12345678, new DateTime( '2003-11-28' ) );

            try {
                $this->service->salvar( $cliente );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'email', 'Email inválido.' );
            }
        });

        it('Lança exceção ao enviar email já existente ao cadastrar cliente', function() {
            allow( $this->service )->toReceive('obterComEmail')->andReturn( new Cliente() );
            allow( $this->service )->toReceive('obterComCpf')->andReturn( [] );

            $cliente = new Cliente( 0, 'Artur Alves', 'arturalvesdeoliveira28@gmail.com', '775.832.150-05', 12345678, new DateTime( '2003-11-28' ) );

            try {
                $this->service->salvar( $cliente );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'email', 'Email já pertence a outro cliente.' );
            }
        });

        it('Lança exceção ao enviar email já existente ao editar cliente', function() {
            allow( $this->service )->toReceive('obterComEmail')->andReturn( new Cliente( 1 ) );
            allow( $this->service )->toReceive('obterComCpf')->andReturn( [] );
            $this->dao->shouldReceive('existe')->andReturn( true );

            $cliente = new Cliente( 2, 'Artur Alves', 'arturalvesdeoliveira28@gmail.com', '775.832.150-05', 12345678, new DateTime( '2003-11-28' ) );

            try {
                $this->service->salvar( $cliente );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'email', 'Email já pertence a outro cliente.' );
            }
        });


        it( 'Lança exceção ao enviar Cpf no formato inválido para cliente', function(){
            allow( $this->service )->toReceive('obterComEmail')->andReturn( [] );
            allow( $this->service )->toReceive('obterComCpf')->andReturn( [] );
            $this->dao->shouldReceive('existe')->andReturn( true );

            $cliente = new Cliente( 2, 'Artur Alves', 'arturalvesdeoliveira28@gmail.com', '77583215005', 12345678, new DateTime( '2003-11-28' ) );

            try {
                $this->service->salvar( $cliente );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'cpf', 'CPF inválido. O formato esperado é 123.456.789-09.' );
            }
        });

        it('Lança exceção ao enviar Cpf já existente ao cadastrar cliente', function() {
            allow( $this->service )->toReceive('obterComEmail')->andReturn( [] );
            allow( $this->service )->toReceive('obterComCpf')->andReturn( new Cliente() );

            $cliente = new Cliente( 0, 'Artur Alves', 'arturalvesdeoliveira28@gmail.com', '775.832.150-05', 12345678, new DateTime( '2003-11-28' ) );

            try {
                $this->service->salvar( $cliente );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'cpf', 'CPF já pertence a outro cliente.' );
            }
        });

        it('Lança exceção ao enviar Cpf já existente ao editar cliente', function() {
            allow( $this->service )->toReceive('obterComEmail')->andReturn( [] );
            allow( $this->service )->toReceive('obterComCpf')->andReturn( new Cliente( 1 ) );
            $this->dao->shouldReceive('existe')->andReturn( true );

            $cliente = new Cliente( 2, 'Artur Alves', 'arturalvesdeoliveira28@gmail.com', '775.832.150-05', 12345678, new DateTime( '2003-11-28' ) );

            try {
                $this->service->salvar( $cliente );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'cpf', 'CPF já pertence a outro cliente.' );
            }
        });


        it('Lança exceção ao enviar senha vazia para cliente', function() {
            allow( $this->service )->toReceive('obterComEmail')->andReturn( [] );
            allow( $this->service )->toReceive('obterComCpf')->andReturn( [] );

            $cliente = new Cliente( 0, 'Artur Alves', 'arturalvesdeoliveira28@gmail.com', '775.832.150-05', '', new DateTime( '2003-11-28' ) );

            try {
                $this->service->salvar( $cliente );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'senha', 'Preencha a senha.' );
            }
        });

        it('Lança exceção ao enviar senha com tamanho diferente que o permitido para cliente', function() {
            allow( $this->service )->toReceive('obterComEmail')->andReturn( [] );
            allow( $this->service )->toReceive('obterComCpf')->andReturn( [] );

            $senhaForaDoTamanhoPermitido = str_repeat('a', ClienteService::TAMANHO_SENHA + 1);
            $cliente = new Cliente( 0, 'Artur Alves', 'arturalvesdeoliveira28@gmail.com', '775.832.150-05', $senhaForaDoTamanhoPermitido, new DateTime( '2003-11-28' ) );

            try {
                $this->service->salvar( $cliente );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'senha', 'A senha deve ter ' . ClienteService::TAMANHO_SENHA . ' caracteres.' );
            }
        });

        it('Lança exceção ao enviar data de nascimento no formato inválido para cliente', function() {
            allow( $this->service )->toReceive('obterComEmail')->andReturn( [] );
            allow( $this->service )->toReceive('obterComCpf')->andReturn( [] );

            $cliente = new Cliente( 0, 'Artur Alves', 'arturalvesdeoliveira28@gmail.com', '775.832.150-05', 12345678, null );

            try {
                $this->service->salvar( $cliente );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'dataNascimento', 'Data de nascimento inválido. O formato esperado é dd/mm/yyyy.' );
            }
        });

        it('Lança exceção ao enviar data de nascimento maior que a data atual para cliente', function() {
            allow( $this->service )->toReceive('obterComEmail')->andReturn( [] );
            allow( $this->service )->toReceive('obterComCpf')->andReturn( [] );

            $dataAtual = new DateTime();
            $dataAtual->modify( '+ 1 days' );
            $cliente = new Cliente( 0, 'Artur Alves', 'arturalvesdeoliveira28@gmail.com', '775.832.150-05', 12345678, $dataAtual );

            try {
                $this->service->salvar( $cliente );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'dataNascimento', 'A data de nascimento precisa ser menor que a data atual.' );
            }
        });
    });

    describe( 'Autenticar', function(){
        it( 'Lança exceção quando cliente não é encontrado', function() {
            $this->dao->shouldReceive('obterComRestricoes')->andReturn( [] );

            expect( function(){
                $this->service->autenticar( 'artur@gmail', 'aaa' );
            } )->toThrow( new NaoAutorizadoException( 'Email ou senha inválidos.' ) );
        });

        it( 'Lança exceção quando email ou senha são inválidos', function(){
            $cliente = new Cliente( 0, 'Artur Alves', 'arturalvesdeoliveira28@gmail.com', '775.832.150-05', 12345678, new DateTime( '2003-11-28' ) );
            $this->dao->shouldReceive('obterComRestricoes')->andReturn( [ $cliente ] );

            expect( function(){
                $this->service->autenticar( 'artur@gmail', 'aaa' );
            } )->toThrow( new NaoAutorizadoException( 'Email ou senha inválidos.' ) );
        });

        it( 'Lança exceção ao gerar token inválido', function(){
            $cliente = new Cliente( 0, 'Artur Alves', 'arturalvesdeoliveira28@gmail.com', '775.832.150-05', 12345678, new DateTime( '2003-11-28' ) );
            $this->dao->shouldReceive('obterComRestricoes')->andReturn( [ $cliente ] );

            allow( $this->service )->toReceive('verificarSenha')->andReturn( true );
            allow( $this->service )->toReceive('gerarToken')->andReturn( null );

            expect( function(){
                $this->service->autenticar( 'arturalvesdeoliveira28@gmail', '12345678' );
            } )->toThrow( new Exception( 'Houve um erro ao gerar o token de acesso.' ) );
        });

        it( 'Autentica cliente corretamente gerando o token', function(){
            $cliente = new Cliente( 0, 'Artur Alves', 'arturalvesdeoliveira28@gmail.com', '775.832.150-05', 12345678, new DateTime( '2003-11-28' ) );
            $this->dao->shouldReceive('obterComRestricoes')->andReturn( [ $cliente ] );

            allow( $this->service )->toReceive('verificarSenha')->andReturn( true );
            allow( $this->service )->toReceive('gerarToken')->andReturn( new TokenJWT( 'aaaaa', 3600 ) );

            $token = $this->service->autenticar( 'arturalvesdeoliveira28@gmail', '12345678' );
            expect( $token )->toBeAnInstanceOf( TokenJWT::class );
        });
    });
});
