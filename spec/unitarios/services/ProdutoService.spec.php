<?php

use app\dao\DAOEmBDR;
use app\exceptions\ServiceException;
use app\classes\Categoria;
use app\classes\Produto;
use app\services\ProdutoService;

describe( 'ProdutoService', function () {
    beforeEach(function () {
        $this->dao = Mockery::mock( DAOEmBDR::class );
        $this->service = new ProdutoService( $this->dao );
    });

    describe( 'Salvar', function(){
        function validarErroSalvar( $e, $campo, $mensagemEsperada ){
            $erro = json_decode( $e->getMessage(), true );
            expect($erro)->not->toBeEmpty();
            expect($erro)->toHaveLength(1);
            expect($erro)->toContainKey($campo);
            expect($erro[$campo])->toEqual($mensagemEsperada);
        }

        $this->nomeProdutoValido = 'Produto Teste';
        $this->referenciaProdutoValida = 'ABCDEFGHIJ';
        $this->corProdutoValida = 'Verde';
        $this->precoProdutoValido = 11.50;
        $this->descricaoProdutoValida = 'Descrição de Produto Teste';
        $this->categoriaProdutoValida = new Categoria( 1, 'Categoria teste', 'Descrição teste' );
        $this->pesoEmGramasValido = 1000;

        describe( 'Nome', function(){
            it('Lança exceção ao enviar nome vazio para produto', function() {
                allow( $this->service )->toReceive('nomeJaCadastrado')->andReturn( false );
                allow( $this->service )->toReceive('referenciaJaCadastrada')->andReturn( false );

                $produto = new Produto( 0, '', $this->referenciaProdutoValida, $this->corProdutoValida, $this->precoProdutoValido, $this->descricaoProdutoValida, $this->categoriaProdutoValida, $this->pesoEmGramasValido );

                try {
                    $this->service->salvar( $produto );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'nome', 'Preencha o nome.' );
                }
            });

            it('Lança exceção ao enviar nome com tamanho menor que o permitido para produto', function() {
                allow( $this->service )->toReceive('nomeJaCadastrado')->andReturn( false );
                allow( $this->service )->toReceive('referenciaJaCadastrada')->andReturn( false );

                $nomeMenorQueOPermitido = str_repeat('a', ProdutoService::TAMANHO_MINIMO_NOME - 1);
                $produto = new Produto( 0, $nomeMenorQueOPermitido, $this->referenciaProdutoValida, $this->corProdutoValida, $this->precoProdutoValido, $this->descricaoProdutoValida, $this->categoriaProdutoValida, $this->pesoEmGramasValido );

                try {
                    $this->service->salvar( $produto );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'nome', 'O nome deve ter entre ' . ProdutoService::TAMANHO_MINIMO_NOME . ' e ' . ProdutoService::TAMANHO_MAXIMO_NOME . ' caracteres.' );
                }
            });

            it('Lança exceção ao enviar nome com tamanho maior que o permitido para produto', function() {
                allow( $this->service )->toReceive('nomeJaCadastrado')->andReturn( false );
                allow( $this->service )->toReceive('referenciaJaCadastrada')->andReturn( false );

                $nomeMaiorQueOPermitido = str_repeat('a', ProdutoService::TAMANHO_MAXIMO_NOME + 1);
                $produto = new Produto( 0, $nomeMaiorQueOPermitido, $this->referenciaProdutoValida, $this->corProdutoValida, $this->precoProdutoValido, $this->descricaoProdutoValida, $this->categoriaProdutoValida, $this->pesoEmGramasValido );

                try {
                    $this->service->salvar( $produto );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'nome', 'O nome deve ter entre ' . ProdutoService::TAMANHO_MINIMO_NOME . ' e ' . ProdutoService::TAMANHO_MAXIMO_NOME . ' caracteres.' );
                }
            });

            it('Lança exceção ao enviar nome já existente ao cadastrar produto', function() {
                $nomeJaCadastrado = 'Nome já cadastrado.';

                allow( $this->service )->toReceive('obterComNome')->andReturn( new Produto( 1, $nomeJaCadastrado ) );
                allow( $this->service )->toReceive('referenciaJaCadastrada')->andReturn( false );

                $produto = new Produto( 0, $nomeJaCadastrado, $this->referenciaProdutoValida, $this->corProdutoValida, $this->precoProdutoValido, $this->descricaoProdutoValida, $this->categoriaProdutoValida, $this->pesoEmGramasValido );

                try {
                    $this->service->salvar( $produto );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'nome', 'Produto já cadastrado com esse nome.' );
                }
            });

            it('Lança exceção ao enviar nome já existente ao editar produto', function() {
                $nomeJaCadastrado = 'Nome já cadastrado.';

                $this->dao->shouldReceive('existe')->andReturn( true );
                allow( $this->service )->toReceive('obterComNome')->andReturn( new Produto( 1, $nomeJaCadastrado ) );
                allow( $this->service )->toReceive('referenciaJaCadastrada')->andReturn( false );

                $produto = new Produto( 3, $nomeJaCadastrado, $this->referenciaProdutoValida, $this->corProdutoValida, $this->precoProdutoValido, $this->descricaoProdutoValida, $this->categoriaProdutoValida, $this->pesoEmGramasValido );

                try {
                    $this->service->salvar( $produto );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'nome', 'Produto já cadastrado com esse nome.' );
                }
            });

            it('Continua execução ao enviar nome já existente ao editar produto quando o produto já existente é o produto a ser editado', function() {
                $nomeJaCadastrado = 'Nome já cadastrado.';

                $this->dao->shouldReceive('existe')->andReturn( true );
                $this->dao->shouldReceive('salvar')->andReturn( true );

                allow( $this->service )->toReceive('obterComNome')->andReturn( new Produto( 1, $nomeJaCadastrado ) );
                allow( $this->service )->toReceive('referenciaJaCadastrada')->andReturn( false );

                $produto = new Produto( 1, $nomeJaCadastrado, $this->referenciaProdutoValida, $this->corProdutoValida, $this->precoProdutoValido, $this->descricaoProdutoValida, $this->categoriaProdutoValida, $this->pesoEmGramasValido );

                expect( function() use ( $produto ){
                    $this->service->salvar( $produto );
                })->not->toThrow();
            });
        } );

        describe( 'Referência', function(){
            it('Lança exceção ao enviar referência vazia para produto', function() {
                allow( $this->service )->toReceive('nomeJaCadastrado')->andReturn( false );
                allow( $this->service )->toReceive('referenciaJaCadastrada')->andReturn( false );

                $produto = new Produto( 0, $this->nomeProdutoValido, '', $this->corProdutoValida, $this->precoProdutoValido, $this->descricaoProdutoValida, $this->categoriaProdutoValida, $this->pesoEmGramasValido );

                try {
                    $this->service->salvar( $produto );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'referencia', 'Preencha a referência.' );
                }
            });

            it('Lança exceção ao enviar referência com tamanho diferente do permitido para produto', function() {
                allow( $this->service )->toReceive('nomeJaCadastrado')->andReturn( false );
                allow( $this->service )->toReceive('referenciaJaCadastrada')->andReturn( false );

                $referenciaDiferenteDoPermitido = str_repeat('a', ProdutoService::TAMANHO_REFERENCIA + 1);
                $produto = new Produto( 0, $this->nomeProdutoValido, $referenciaDiferenteDoPermitido, $this->corProdutoValida, $this->precoProdutoValido, $this->descricaoProdutoValida, $this->categoriaProdutoValida, $this->pesoEmGramasValido );

                try {
                    $this->service->salvar( $produto );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'referencia', 'A referência deve ter ' . ProdutoService::TAMANHO_REFERENCIA . ' caracteres.' );
                }
            });

            it('Lança exceção ao enviar referência já existente ao cadastrar produto', function() {
                $referenciaJaCadastrada = 'REFERENCIA';

                allow( $this->service )->toReceive('obterComReferencia')->andReturn( new Produto( 1, $referenciaJaCadastrada ) );
                allow( $this->service )->toReceive('nomeJaCadastrado')->andReturn( false );

                $produto = new Produto( 0,  $this->nomeProdutoValido, $referenciaJaCadastrada, $this->corProdutoValida, $this->precoProdutoValido, $this->descricaoProdutoValida, $this->categoriaProdutoValida, $this->pesoEmGramasValido );

                try {
                    $this->service->salvar( $produto );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'referencia', 'Produto já cadastrado com essa referência.' );
                }
            });

            it('Lança exceção ao enviar referência já existente ao editar produto', function() {
                $referenciaJaCadastrada = 'REFERENCIA';

                $this->dao->shouldReceive('existe')->andReturn( true );
                allow( $this->service )->toReceive('obterComReferencia')->andReturn( new Produto( 1, $referenciaJaCadastrada ) );
                allow( $this->service )->toReceive('nomeJaCadastrado')->andReturn( false );

                $produto = new Produto( 3, $this->nomeProdutoValido, $referenciaJaCadastrada, $this->corProdutoValida, $this->precoProdutoValido, $this->descricaoProdutoValida, $this->categoriaProdutoValida, $this->pesoEmGramasValido );

                try {
                    $this->service->salvar( $produto );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'referencia', 'Produto já cadastrado com essa referência.' );
                }
            });

            it('Continua execução ao enviar nome já existente ao editar produto quando o produto já existente é o produto a ser editado', function() {
                $referenciaJaCadastrada = 'REFERENCIA';

                $this->dao->shouldReceive('existe')->andReturn( true );
                $this->dao->shouldReceive('salvar')->andReturn( true );

                allow( $this->service )->toReceive('obterComNome')->andReturn( new Produto( 1, $referenciaJaCadastrada ) );
                allow( $this->service )->toReceive('referenciaJaCadastrada')->andReturn( false );

                $produto = new Produto( 1, $this->nomeProdutoValido, $referenciaJaCadastrada, $this->corProdutoValida, $this->precoProdutoValido, $this->descricaoProdutoValida, $this->categoriaProdutoValida, $this->pesoEmGramasValido );

                expect( function() use ( $produto ){
                    $this->service->salvar( $produto );
                })->not->toThrow();
            });
        } );

        describe( 'Cor', function(){
            it('Lança exceção ao enviar cor vazia para produto', function() {
                allow( $this->service )->toReceive('nomeJaCadastrado')->andReturn( false );
                allow( $this->service )->toReceive('referenciaJaCadastrada')->andReturn( false );

                $produto = new Produto( 0, $this->nomeProdutoValido, $this->referenciaProdutoValida, '', $this->precoProdutoValido, $this->descricaoProdutoValida, $this->categoriaProdutoValida, $this->pesoEmGramasValido );

                try {
                    $this->service->salvar( $produto );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'cor', 'Preencha a cor.' );
                }
            });

            it('Lança exceção ao enviar cor com tamanho menor que o permitido para produto', function() {
                allow( $this->service )->toReceive('nomeJaCadastrado')->andReturn( false );
                allow( $this->service )->toReceive('referenciaJaCadastrada')->andReturn( false );

                $corMenorQueOPermitido = str_repeat('a', ProdutoService::TAMANHO_MINIMO_COR - 1);
                $produto = new Produto( 0, $this->nomeProdutoValido, $this->referenciaProdutoValida, $corMenorQueOPermitido, $this->precoProdutoValido, $this->descricaoProdutoValida, $this->categoriaProdutoValida, $this->pesoEmGramasValido );

                try {
                    $this->service->salvar( $produto );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'cor', 'A cor deve ter entre ' . ProdutoService::TAMANHO_MINIMO_COR . ' e ' . ProdutoService::TAMANHO_MAXIMO_COR . ' caracteres.' );
                }
            });

            it('Lança exceção ao enviar cor com tamanho maior que o permitido para produto', function() {
                allow( $this->service )->toReceive('nomeJaCadastrado')->andReturn( false );
                allow( $this->service )->toReceive('referenciaJaCadastrada')->andReturn( false );

                $corMaiorQueOPermitido = str_repeat('a', ProdutoService::TAMANHO_MAXIMO_COR + 1);
                $produto = new Produto( 0, $this->nomeProdutoValido, $this->referenciaProdutoValida, $corMaiorQueOPermitido, $this->precoProdutoValido, $this->descricaoProdutoValida, $this->categoriaProdutoValida, $this->pesoEmGramasValido );

                try {
                    $this->service->salvar( $produto );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'cor', 'A cor deve ter entre ' . ProdutoService::TAMANHO_MINIMO_COR . ' e ' . ProdutoService::TAMANHO_MAXIMO_COR . ' caracteres.' );
                }
            });
        } );

        describe( 'Preço', function(){
            it('Lança exceção ao enviar preço menor que o permitido para produto', function() {
                allow( $this->service )->toReceive('nomeJaCadastrado')->andReturn( false );
                allow( $this->service )->toReceive('referenciaJaCadastrada')->andReturn( false );

                $precoMenorQueOPermitido = ProdutoService::PRECO_MINIMO - 1;
                $produto = new Produto( 0, $this->nomeProdutoValido, $this->referenciaProdutoValida, $this->corProdutoValida, $precoMenorQueOPermitido, $this->descricaoProdutoValida, $this->categoriaProdutoValida, $this->pesoEmGramasValido );

                try {
                    $this->service->salvar( $produto );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'preco', 'O preço deve estar entre R$ ' . ProdutoService::PRECO_MINIMO . ' e R$ ' . ProdutoService::PRECO_MAXIMO . '.' );
                }
            });

            it('Lança exceção ao enviar preço maior que o permitido para produto', function() {
                allow( $this->service )->toReceive('nomeJaCadastrado')->andReturn( false );
                allow( $this->service )->toReceive('referenciaJaCadastrada')->andReturn( false );

                $precoMaiorQueOPermitido = ProdutoService::PRECO_MAXIMO + 1;
                $produto = new Produto( 0, $this->nomeProdutoValido, $this->referenciaProdutoValida, $this->corProdutoValida, $precoMaiorQueOPermitido, $this->descricaoProdutoValida, $this->categoriaProdutoValida, $this->pesoEmGramasValido );

                try {
                    $this->service->salvar( $produto );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'preco', 'O preço deve estar entre R$ ' . ProdutoService::PRECO_MINIMO . ' e R$ ' . ProdutoService::PRECO_MAXIMO . '.' );
                }
            });
        } );

        describe( 'Descrição', function(){
            it('Lança exceção ao enviar descrição vazia para produto', function() {
                allow( $this->service )->toReceive('nomeJaCadastrado')->andReturn( false );
                allow( $this->service )->toReceive('referenciaJaCadastrada')->andReturn( false );

                $produto = new Produto( 0, $this->nomeProdutoValido, $this->referenciaProdutoValida, $this->corProdutoValida, $this->precoProdutoValido, '', $this->categoriaProdutoValida, $this->pesoEmGramasValido );

                try {
                    $this->service->salvar( $produto );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'descricao', 'Preencha a descrição.' );
                }
            });

            it('Lança exceção ao enviar descrição com tamanho menor que o permitido para produto', function() {
                allow( $this->service )->toReceive('nomeJaCadastrado')->andReturn( false );
                allow( $this->service )->toReceive('referenciaJaCadastrada')->andReturn( false );

                $descricaoMenorQueOPermitido = str_repeat('a', ProdutoService::TAMANHO_MINIMO_DESCRICAO - 1);
                $produto = new Produto( 0, $this->nomeProdutoValido, $this->referenciaProdutoValida, $this->corProdutoValida, $this->precoProdutoValido, $descricaoMenorQueOPermitido, $this->categoriaProdutoValida, $this->pesoEmGramasValido );

                try {
                    $this->service->salvar( $produto );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'descricao', 'A descrição deve ter entre ' . ProdutoService::TAMANHO_MINIMO_DESCRICAO . ' e ' . ProdutoService::TAMANHO_MAXIMO_DESCRICAO . ' caracteres.' );
                }
            });

            it('Lança exceção ao enviar cor com tamanho maior que o permitido para produto', function() {
                allow( $this->service )->toReceive('nomeJaCadastrado')->andReturn( false );
                allow( $this->service )->toReceive('referenciaJaCadastrada')->andReturn( false );

                $descricaoMaiorQueOPermitido = str_repeat('a', ProdutoService::TAMANHO_MAXIMO_DESCRICAO + 1);
                $produto = new Produto( 0, $this->nomeProdutoValido, $this->referenciaProdutoValida, $this->corProdutoValida, $this->precoProdutoValido, $descricaoMaiorQueOPermitido, $this->categoriaProdutoValida, $this->pesoEmGramasValido );

                try {
                    $this->service->salvar( $produto );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'descricao', 'A descrição deve ter entre ' . ProdutoService::TAMANHO_MINIMO_DESCRICAO . ' e ' . ProdutoService::TAMANHO_MAXIMO_DESCRICAO . ' caracteres.' );
                }
            });
        } );

        describe( 'Categoria', function(){
            it('Lança exceção ao enviar preço maior que o permitido para produto', function() {
                allow( $this->service )->toReceive('nomeJaCadastrado')->andReturn( false );
                allow( $this->service )->toReceive('referenciaJaCadastrada')->andReturn( false );

                $produto = new Produto( 0, $this->nomeProdutoValido, $this->referenciaProdutoValida, $this->corProdutoValida, $this->precoProdutoValido, $this->descricaoProdutoValida, null, $this->pesoEmGramasValido );

                try {
                    $this->service->salvar( $produto );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'categoria', 'Categoria não encontrada.' );
                }
            });
        } );

        describe( 'Peso em Gramas', function(){
            it('Lança exceção ao enviar peso menor que o permitido para produto', function() {
                allow( $this->service )->toReceive('nomeJaCadastrado')->andReturn( false );
                allow( $this->service )->toReceive('referenciaJaCadastrada')->andReturn( false );

                $pesoMenorQueOPermitido = ProdutoService::PESO_MINIMO - 1;
                $produto = new Produto( 0, $this->nomeProdutoValido, $this->referenciaProdutoValida, $this->corProdutoValida, $this->precoProdutoValido, $this->descricaoProdutoValida, $this->categoriaProdutoValida, $pesoMenorQueOPermitido );

                try {
                    $this->service->salvar( $produto );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'pesoEmGramas', 'O peso deve estar entre ' . ProdutoService::PRECO_MINIMO . 'g e ' . ProdutoService::PRECO_MAXIMO . 'g.' );
                }
            });

            it('Lança exceção ao enviar peso maior que o permitido para produto', function() {
                allow( $this->service )->toReceive('nomeJaCadastrado')->andReturn( false );
                allow( $this->service )->toReceive('referenciaJaCadastrada')->andReturn( false );

                $pesoMaiorQueOPermitido = ProdutoService::PESO_MAXIMO + 1;
                $produto = new Produto( 0, $this->nomeProdutoValido, $this->referenciaProdutoValida, $this->corProdutoValida, $this->precoProdutoValido, $this->descricaoProdutoValida, $this->categoriaProdutoValida, $pesoMaiorQueOPermitido );

                try {
                    $this->service->salvar( $produto );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'pesoEmGramas', 'O peso deve estar entre ' . ProdutoService::PRECO_MINIMO . 'g e ' . ProdutoService::PRECO_MAXIMO . 'g.' );
                }
            });
        } );
    });
});
