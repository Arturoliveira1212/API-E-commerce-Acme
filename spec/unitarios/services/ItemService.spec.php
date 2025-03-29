<?php

use app\classes\enum\OperacaoEstoque;
use app\dao\DAOEmBDR;
use app\exceptions\ServiceException;
use app\classes\Item;
use app\exceptions\NaoEncontradoException;
use app\services\ItemService;

describe( 'ItemService', function () {
    beforeEach(function () {
        $this->dao = Mockery::mock( DAOEmBDR::class );
        $this->service = new ItemService( $this->dao );
    });

    $this->skuItemValido = str_repeat( '1', ItemService::TAMANHO_SKU );
    $this->tamanhoItemValido = ItemService::TAMANHOS_DISPONIVEIS[0];
    $this->estoqueItemValido = ItemService::ESTOQUE_MINIMO;
    $this->pesoEmGramasItemValido = rand( ItemService::PESO_MINIMO, ItemService::PESO_MAXIMO );

    describe( 'Salvar', function(){

        describe( 'Sku', function(){
            it('Lança exceção ao enviar sku vazio para item', function() {
                allow( $this->service )->toReceive('produtoDoItemExiste')->andReturn( true );
                allow( $this->service )->toReceive('skuJaCadastrado')->andReturn( false );

                $item = new Item( 0, '', $this->tamanhoItemValido, $this->estoqueItemValido, $this->pesoEmGramasItemValido );

                try {
                    $this->service->salvar( $item );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'sku', 'Preencha o sku.' );
                }
            });

            it('Lança exceção ao enviar sku com tamanho maior que o permitido para item', function() {
                allow( $this->service )->toReceive('produtoDoItemExiste')->andReturn( true );
                allow( $this->service )->toReceive('skuJaCadastrado')->andReturn( false );

                $skuForaDoTamanhoPermitido = str_repeat( 'a', ItemService::TAMANHO_SKU + 1 );
                $item = new Item( 0, $skuForaDoTamanhoPermitido, $this->tamanhoItemValido, $this->estoqueItemValido, $this->pesoEmGramasItemValido );

                try {
                    $this->service->salvar( $item );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'sku',  'O sku deve ter ' . ItemService::TAMANHO_SKU . ' caracteres, sendo eles números e letras.' );
                }
            });

            it('Lança exceção ao enviar sku com tamanho menor que o permitido para item', function() {
                allow( $this->service )->toReceive('produtoDoItemExiste')->andReturn( true );
                allow( $this->service )->toReceive('skuJaCadastrado')->andReturn( false );

                $skuForaDoTamanhoPermitido = str_repeat( 'a', ItemService::TAMANHO_SKU - 1 );
                $item = new Item( 0, $skuForaDoTamanhoPermitido, $this->tamanhoItemValido, $this->estoqueItemValido, $this->pesoEmGramasItemValido );

                try {
                    $this->service->salvar( $item );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'sku',  'O sku deve ter ' . ItemService::TAMANHO_SKU . ' caracteres, sendo eles números e letras.' );
                }
            });

            it('Lança exceção ao enviar sku com caracteres não permitidos para item', function() {
                allow( $this->service )->toReceive('produtoDoItemExiste')->andReturn( true );
                allow( $this->service )->toReceive('skuJaCadastrado')->andReturn( false );

                $skuComCaracteresNaoPermitidos = '@#$%f&*(';
                $item = new Item( 0, $skuComCaracteresNaoPermitidos, $this->tamanhoItemValido, $this->estoqueItemValido, $this->pesoEmGramasItemValido );

                try {
                    $this->service->salvar( $item );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'sku',  'O sku deve ter ' . ItemService::TAMANHO_SKU . ' caracteres, sendo eles números e letras.' );
                }
            });

            it('Lança exceção ao enviar sku já existente ao cadastrar item', function() {
                $skuJaCadastrado = $this->skuItemValido;

                allow( $this->service )->toReceive('produtoDoItemExiste')->andReturn( true );
                allow( $this->service )->toReceive('obterComSku')->andReturn( new Item( 1, $skuJaCadastrado ) );

                $item = new Item( 0, $skuJaCadastrado, $this->tamanhoItemValido, $this->estoqueItemValido, $this->pesoEmGramasItemValido );

                try {
                    $this->service->salvar( $item );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'sku', 'Item já cadastrado com esse sku.' );
                }
            });

            it('Lança exceção ao enviar sku já existente ao editar item', function() {
                $skuJaCadastrado = $this->skuItemValido;

                $this->dao->shouldReceive('existe')->andReturn( true );
                allow( $this->service )->toReceive('produtoDoItemExiste')->andReturn( true );
                allow( $this->service )->toReceive('obterComSku')->andReturn( new Item( 1, $skuJaCadastrado ) );

                $item = new Item( 3, $skuJaCadastrado, $this->tamanhoItemValido, $this->estoqueItemValido, $this->pesoEmGramasItemValido );

                try {
                    $this->service->salvar( $item );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'sku', 'Item já cadastrado com esse sku.' );
                }
            });

            it('Continua execução ao enviar sku já existente ao editar item quando o item já existente é o próprio item a ser editado', function() {
                $skuJaCadastrado = $this->skuItemValido;

                $this->dao->shouldReceive('existe')->andReturn( true );
                $this->dao->shouldReceive('salvar')->andReturn( true );

                allow( $this->service )->toReceive('produtoDoItemExiste')->andReturn( true );
                allow( $this->service )->toReceive('obterComSku')->andReturn( new Item( 1, $skuJaCadastrado ) );

                $item = new Item( 1, $skuJaCadastrado, $this->tamanhoItemValido, $this->estoqueItemValido, $this->pesoEmGramasItemValido );

                expect( function() use ( $item ){
                    $this->service->salvar( $item );
                })->not->toThrow();
            });
        } );

        describe( 'Tamanho', function(){
            it('Lança exceção ao enviar tamanho vazio para item', function() {
                allow( $this->service )->toReceive('produtoDoItemExiste')->andReturn( true );
                allow( $this->service )->toReceive('skuJaCadastrado')->andReturn( false );

                $item = new Item( 0, $this->skuItemValido, '', $this->estoqueItemValido, $this->pesoEmGramasItemValido );

                try {
                    $this->service->salvar( $item );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'tamanho', 'Preencha o tamanho.' );
                }
            });

            it('Lança exceção ao enviar tamanho não permitido para item', function() {
                allow( $this->service )->toReceive('produtoDoItemExiste')->andReturn( true );
                allow( $this->service )->toReceive('skuJaCadastrado')->andReturn( false );

                $tamanhoNaoPermitido = 'fsdnkfsdkjfsd';
                $item = new Item( 0, $this->skuItemValido, $tamanhoNaoPermitido, $this->estoqueItemValido, $this->pesoEmGramasItemValido );

                try {
                    $this->service->salvar( $item );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'tamanho', 'Tamanho inválido. Os tamanhos disponíveis são: ' . implode( ',', ItemService::TAMANHOS_DISPONIVEIS ) . '.' );
                }
            });
        } );

        describe( 'Peso em Gramas', function(){
            it('Lança exceção ao enviar peso maior que o permitido para item', function() {
                allow( $this->service )->toReceive('produtoDoItemExiste')->andReturn( true );
                allow( $this->service )->toReceive('skuJaCadastrado')->andReturn( false );

                $pesoMaior = ItemService::PESO_MAXIMO + 1;
                $item = new Item( 0, $this->skuItemValido, $this->tamanhoItemValido, $this->estoqueItemValido, $pesoMaior );

                try {
                    $this->service->salvar( $item );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'pesoEmGramas',  'O peso deve estar entre ' . ItemService::PESO_MINIMO . 'g e ' . ItemService::PESO_MAXIMO . 'g.' );
                }
            });

            it('Lança exceção ao enviar peso menor que o permitido para item', function() {
                allow( $this->service )->toReceive('produtoDoItemExiste')->andReturn( true );
                allow( $this->service )->toReceive('skuJaCadastrado')->andReturn( false );

                $pesoMenor = ItemService::PESO_MINIMO - 1;
                $item = new Item( 0, $this->skuItemValido, $this->tamanhoItemValido, $this->estoqueItemValido, $pesoMenor );

                try {
                    $this->service->salvar( $item );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'pesoEmGramas', 'O peso deve estar entre ' . ItemService::PESO_MINIMO . 'g e ' . ItemService::PESO_MAXIMO . 'g.' );
                }
            });
        } );

        describe( 'Estoque', function(){
            it('Lança exceção ao enviar estoque maior que o permitido para item', function() {
                allow( $this->service )->toReceive('produtoDoItemExiste')->andReturn( true );
                allow( $this->service )->toReceive('skuJaCadastrado')->andReturn( false );

                $estoqueMaior = ItemService::ESTOQUE_MAXIMO + 1;
                $item = new Item( 0, $this->skuItemValido, $this->tamanhoItemValido, $estoqueMaior, $this->pesoEmGramasItemValido );

                try {
                    $this->service->salvar( $item );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'estoque', 'O estoque deve estar entre ' . ItemService::ESTOQUE_MINIMO . ' e ' . ItemService::ESTOQUE_MAXIMO . ' unidades.' );
                }
            });

            it('Lança exceção ao enviar estoque menor que o permitido para item', function() {
                allow( $this->service )->toReceive('produtoDoItemExiste')->andReturn( true );
                allow( $this->service )->toReceive('skuJaCadastrado')->andReturn( false );

                $estoqueMenor = ItemService::ESTOQUE_MINIMO - 1;
                $item = new Item( 0, $this->skuItemValido, $this->tamanhoItemValido, $estoqueMenor, $this->pesoEmGramasItemValido );

                try {
                    $this->service->salvar( $item );
                } catch( ServiceException $e ){
                    validarErroSalvar( $e, 'estoque', 'O estoque deve estar entre ' . ItemService::ESTOQUE_MINIMO . ' e ' . ItemService::ESTOQUE_MAXIMO . ' unidades.' );
                }
            });
        } );
    });

    describe( 'Movimentar Estoque', function(){
        it('Lança exceção ao enviar idItem inválido', function() {
            $this->dao->shouldReceive('obterComId')->andReturn( [] );

            expect( function(){
                $this->service->movimentarEstoque( 1, 10, OperacaoEstoque::ADICIONAR );
            })->toThrow( new NaoEncontradoException( 'Recurso não encontrado.' ) );
        });

        it('Lança exceção ao enviar operação estoque inválida', function() {
            $item = new Item( 1, $this->skuItemValido, $this->tamanhoItemValido, $this->estoqueItemValido, $this->pesoEmGramasItemValido );
            $this->dao->shouldReceive('obterComId')->andReturn( $item );

            try {
                $this->service->movimentarEstoque( $item->getId(), 10, 2332 );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'operacao', 'Operação inválida.' );
            }
        });

        it('Lança exceção ao enviar estoque maior que o permitido', function() {
            $estoqueMaior = ItemService::ESTOQUE_MAXIMO + 1;
            $item = new Item( 1, $this->skuItemValido, $this->tamanhoItemValido, $this->estoqueItemValido, $this->pesoEmGramasItemValido );
            $this->dao->shouldReceive('obterComId')->andReturn( $item );

            try {
                $this->service->movimentarEstoque( $item->getId(), $estoqueMaior, OperacaoEstoque::ADICIONAR );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'quantidade', 'A quantidade deve estar entre ' . ItemService::QUANTIDADE_MINIMA . ' e ' . ItemService::QUANTIDADE_MAXIMA . ' unidades.' );
            }
        });

        it('Lança exceção ao enviar estoque menor que o permitido', function() {
            $estoqueMenor = ItemService::ESTOQUE_MINIMO - 1;
            $item = new Item( 1, $this->skuItemValido, $this->tamanhoItemValido, $this->estoqueItemValido, $this->pesoEmGramasItemValido );
            $this->dao->shouldReceive('obterComId')->andReturn( $item );

            try {
                $this->service->movimentarEstoque( $item->getId(), $estoqueMenor, OperacaoEstoque::ADICIONAR );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'quantidade', 'A quantidade deve estar entre ' . ItemService::QUANTIDADE_MINIMA . ' e ' . ItemService::QUANTIDADE_MAXIMA . ' unidades.' );
            }
        });

        it('Lança exceção ao tentar remover quantidade maior do que a presente em estoque', function() {
            $estoqueMaiorQueODisponivel = $this->estoqueItemValido + 1;
            $item = new Item( 1, $this->skuItemValido, $this->tamanhoItemValido, $this->estoqueItemValido, $this->pesoEmGramasItemValido );
            $this->dao->shouldReceive('obterComId')->andReturn( $item );

            try {
                $this->service->movimentarEstoque( $item->getId(), $estoqueMaiorQueODisponivel, OperacaoEstoque::REMOVER );
            } catch( ServiceException $e ){
                validarErroSalvar( $e, 'quantidade', 'O item não possui estoque disponível para a remoção.' );
            }
        });
    });
});
