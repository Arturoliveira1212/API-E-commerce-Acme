<?php

use app\classes\Endereco;
use app\dao\DAOEmBDR;
use app\services\ClienteService;
use app\services\EnderecoService;
use app\exceptions\ServiceException;
use app\exceptions\NaoEncontradoException;

describe( 'EnderecoService', function () {
    beforeEach(function () {
        $this->dao = Mockery::mock( DAOEmBDR::class );
        $this->service = new EnderecoService( $this->dao );
        $this->clienteService = Mockery::mock( ClienteService::class );
    });

    describe( 'Salvar', function(){
        function validarErroSalvar( $e, $campo, $mensagemEsperada ){
            $erro = json_decode( $e->getMessage(), true );
            expect($erro)->not->toBeEmpty();
            expect($erro)->toHaveLength(1);
            expect($erro)->toContainKey($campo);
            expect($erro[$campo])->toEqual($mensagemEsperada);
        }

        it('Lança exceção ao enviar cliente inexistente para endereço', function() {
            allow($this->service)->toReceive('clienteDoEnderecoExiste')->andReturn( false );

            expect( function(){
                $this->service->salvar( new Endereco(), 1 );
            } )->toThrow( new NaoEncontradoException( 'Recurso não encontrado.' ) );
        });

        it('Lança exceção ao enviar logradouro vazio para endereço', function() {
            allow($this->service)->toReceive('clienteDoEnderecoExiste')->andReturn( true );

            $endereco = new Endereco( 0, '', 'São Paulo', 'Higienóplis', 'SN', '28655000' );

            try {
                $this->service->salvar( $endereco, 1 );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'logradouro', 'Preencha o logradouro.' );
            }
        });

        it('Lança exceção ao enviar logradouro com tamanho menor que o permitido vazio para endereço', function() {
            allow($this->service)->toReceive('clienteDoEnderecoExiste')->andReturn( true );

            $logradouroMenorQueOPermitido = str_repeat('a', EnderecoService::TAMANHO_MINIMO_LOGRADOURO - 1);
            $endereco = new Endereco( 0, $logradouroMenorQueOPermitido, 'São Paulo', 'Higienóplis', 'SN', '28655000' );

            try {
                $this->service->salvar( $endereco, 1 );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'logradouro', 'O logradouro deve ter entre ' . EnderecoService::TAMANHO_MINIMO_LOGRADOURO . ' e ' . EnderecoService::TAMANHO_MAXIMO_LOGRADOURO . ' caracteres.' );
            }
        });

        it('Lança exceção ao enviar logradouro com tamanho maior que o permitido vazio para endereço', function() {
            allow($this->service)->toReceive('clienteDoEnderecoExiste')->andReturn( true );

            $logradouroMaiorQueOPermitido = str_repeat('a', EnderecoService::TAMANHO_MAXIMO_LOGRADOURO + 1);
            $endereco = new Endereco( 0, $logradouroMaiorQueOPermitido, 'São Paulo', 'Higienóplis', 'SN', '28655000' );

            try {
                $this->service->salvar( $endereco, 1 );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'logradouro', 'O logradouro deve ter entre ' . EnderecoService::TAMANHO_MINIMO_LOGRADOURO . ' e ' . EnderecoService::TAMANHO_MAXIMO_LOGRADOURO . ' caracteres.' );
            }
        });

        it('Lança exceção ao enviar cidade vazio para endereço', function() {
            allow($this->service)->toReceive('clienteDoEnderecoExiste')->andReturn( true );

            $endereco = new Endereco( 0, 'Rua X da Silva', '', 'Higienóplis', 'SN', '28655000' );

            try {
                $this->service->salvar( $endereco, 1 );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'cidade', 'Preencha a cidade.' );
            }
        });

        it('Lança exceção ao enviar cidade com tamanho menor que o permitido vazio para endereço', function() {
            allow($this->service)->toReceive('clienteDoEnderecoExiste')->andReturn( true );

            $cidadeMenorQueOPermitido = str_repeat('a', EnderecoService::TAMANHO_MINIMO_CIDADE - 1);
            $endereco = new Endereco( 0, 'Rua X da Silva', $cidadeMenorQueOPermitido, 'Higienóplis', 'SN', '28655000' );

            try {
                $this->service->salvar( $endereco, 1 );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'cidade', 'A cidade deve ter entre ' . EnderecoService::TAMANHO_MINIMO_CIDADE . ' e ' . EnderecoService::TAMANHO_MAXIMO_CIDADE . ' caracteres.' );
            }
        });

        it('Lança exceção ao enviar cidade com tamanho maior que o permitido vazio para endereço', function() {
            allow($this->service)->toReceive('clienteDoEnderecoExiste')->andReturn( true );

            $cidadeMaiorQueOPermitido = str_repeat('a', EnderecoService::TAMANHO_MAXIMO_LOGRADOURO + 1);
            $endereco = new Endereco( 0, 'Rua X da Silva', $cidadeMaiorQueOPermitido, 'Higienóplis', 'SN', '28655000' );

            try {
                $this->service->salvar( $endereco, 1 );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'cidade', 'A cidade deve ter entre ' . EnderecoService::TAMANHO_MINIMO_CIDADE . ' e ' . EnderecoService::TAMANHO_MAXIMO_CIDADE . ' caracteres.' );
            }
        });

        it('Lança exceção ao enviar número com tamanho maior que o permitido vazio para endereço', function() {
            allow($this->service)->toReceive('clienteDoEnderecoExiste')->andReturn( true );

            $numeroMaiorQueOPermitido = str_repeat('a', EnderecoService::TAMANHO_MAXIMO_LOGRADOURO + 1);
            $endereco = new Endereco( 0, 'Rua X da Silva', 'Cidade', 'Higienóplis', $numeroMaiorQueOPermitido, '28655000' );

            try {
                $this->service->salvar( $endereco, 1 );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'numero', 'O número deve ter no máximo ' . EnderecoService::TAMANHO_MAXIMO_NUMERO . ' caracteres.' );
            }
        });

        it('Continua exceção ao não enviar número para endereço', function() {
            allow($this->service)->toReceive('clienteDoEnderecoExiste')->andReturn( true );
            $this->dao->shouldReceive('salvar')->andReturn( true );

            // $numeroMaiorQueOPermitido = str_repeat('a', EnderecoService::TAMANHO_MAXIMO_LOGRADOURO + 1);
            $endereco = new Endereco( 0, 'Rua X da Silva', 'Cidade', 'Higienóplis', '', '28655000' );

            expect( function() use ( $endereco ){
                $this->service->salvar( $endereco, 1 );
            } )->not->toThrow();
        });

        it('Lança exceção ao enviar cep vazio para endereço', function() {
            allow($this->service)->toReceive('clienteDoEnderecoExiste')->andReturn( true );

            $endereco = new Endereco( 0, 'Rua X da Silva', 'São Paulo', 'Higienóplis', 'SN', '' );

            try {
                $this->service->salvar( $endereco, 1 );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'cep', 'Preencha o cep.' );
            }
        });

        it('Lança exceção ao enviar cep com tamanho diferente do permitido para endereço', function() {
            allow($this->service)->toReceive('clienteDoEnderecoExiste')->andReturn( true );

            $cepComTamanhoForaDoPermitido = str_repeat('a', EnderecoService::TAMANHO_CEP - 1);
            $endereco = new Endereco( 0, 'Rua X da Silva', 'São Paulo', 'Higienóplis', 'SN', $cepComTamanhoForaDoPermitido );

            try {
                $this->service->salvar( $endereco, 1 );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'cep', 'O cep deve ter ' . EnderecoService::TAMANHO_CEP . ' caracteres.' );
            }
        });

        it('Lança exceção ao enviar complemento com tamanho maior que o permitido vazio para endereço', function() {
            allow($this->service)->toReceive('clienteDoEnderecoExiste')->andReturn( true );

            $complementoMaiorQueOPermitido = str_repeat('a', EnderecoService::TAMANHO_MAXIMO_COMPLEMENTO + 1);
            $endereco = new Endereco( 0, 'Rua X da Silva', 'Cidade', 'Higienóplis', 'SN', '28655000', $complementoMaiorQueOPermitido );

            try {
                $this->service->salvar( $endereco, 1 );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'complemento', 'O complemento deve ter no máximo ' . EnderecoService::TAMANHO_MAXIMO_COMPLEMENTO . ' caracteres.' );
            }
        });

        it('Continua exceção ao não enviar complemento para endereço', function() {
            allow($this->service)->toReceive('clienteDoEnderecoExiste')->andReturn( true );
            $this->dao->shouldReceive('salvar')->andReturn( true );

            $endereco = new Endereco( 0, 'Rua X da Silva', 'Cidade', 'Higienóplis', 'SN', '28655000' );

            expect( function() use ( $endereco ){
                $this->service->salvar( $endereco, 1 );
            } )->not->toThrow();
        });
    });
});
