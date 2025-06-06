<?php

use app\dao\DAOEmBDR;
use app\exceptions\ServiceException;
use app\classes\Administrador;
use app\classes\jwt\TokenJWT;
use app\exceptions\NaoAutorizadoException;
use app\exceptions\NaoEncontradoException;
use app\services\AdministradorService;

describe('AdministradorService', function () {
    beforeEach(function () {
        $this->dao = Mockery::mock(DAOEmBDR::class);
        $this->service = new AdministradorService($this->dao);
    });

    describe('Salvar', function () {
        it('Lança exceção ao tentar editar administrador master', function () {
            $this->dao->shouldReceive('existe')->andReturn(true);

            $administrador = new Administrador(1, 'Admin master', 'arturalvesdeoliveira28@gmail.com', 12345678);

            try {
                $this->service->salvar($administrador);
            } catch (ServiceException $e) {
                validarErroSalvar($e, 'administrador', 'Não é possível editar o administrador master.');
            }
        });

        it('Lança exceção ao enviar nome vazio para administrador', function () {
            $this->dao->shouldReceive('obterComRestricoes')->andReturn([]);

            $administrador = new Administrador(0, '', 'arturalvesdeoliveira28@gmail.com', 12345678);

            try {
                $this->service->salvar($administrador);
            } catch (ServiceException $e) {
                validarErroSalvar($e, 'nome', 'Preencha o nome.');
            }
        });

        it('Lança exceção ao enviar nome com tamanho maior que o permitido para administrador', function () {
            $this->dao->shouldReceive('obterComRestricoes')->andReturn([]);

            $nomeForaDoTamanhoPermitido = str_repeat('a', AdministradorService::TAMANHO_MAXIMO_NOME + 1);
            $administrador = new Administrador(0, $nomeForaDoTamanhoPermitido, 'arturalvesdeoliveira28@gmail.com', 12345678);

            try {
                $this->service->salvar($administrador);
            } catch (ServiceException $e) {
                validarErroSalvar($e, 'nome', 'O nome deve ter entre ' . AdministradorService::TAMANHO_MINIMO_NOME . ' e ' . AdministradorService::TAMANHO_MAXIMO_NOME . ' caracteres.');
            }
        });

        it('Lança exceção ao enviar email vazio para administrador', function () {
            $administrador = new Administrador(0, 'Artur Alves', '', 12345678);

            try {
                $this->service->salvar($administrador);
            } catch (ServiceException $e) {
                validarErroSalvar($e, 'email', 'Preencha o email.');
            }
        });

        it('Lança exceção ao enviar email com tamanho maior que o permitido para administrador', function () {
            $emailForaDoTamanhoPermitido = str_repeat('a', AdministradorService::TAMANHO_MAXIMO_EMAIL + 1);
            $administrador = new Administrador(0, 'Artur Alves', $emailForaDoTamanhoPermitido, 12345678);

            try {
                $this->service->salvar($administrador);
            } catch (ServiceException $e) {
                validarErroSalvar($e, 'email', 'O email deve ter entre ' . AdministradorService::TAMANHO_MINIMO_EMAIL . ' e ' . AdministradorService::TAMANHO_MAXIMO_EMAIL . ' caracteres.');
            }
        });

        it('Lança exceção ao enviar email inválido para administrador', function () {
            $administrador = new Administrador(0, 'Artur Alves', 'aaaaaa', 12345678);

            try {
                $this->service->salvar($administrador);
            } catch (ServiceException $e) {
                validarErroSalvar($e, 'email', 'Email inválido.');
            }
        });

        it('Lança exceção ao enviar email já existente ao cadastrar administrador', function () {
            $this->dao->shouldReceive('obterComRestricoes')->andReturn([ new Administrador() ]);

            $administrador = new Administrador(0, 'Artur Alves', 'arturalvesdeoliveira28@gmail.com', 12345678);

            try {
                $this->service->salvar($administrador);
            } catch (ServiceException $e) {
                validarErroSalvar($e, 'email', 'Email já pertence a outro administrador.');
            }
        });

        it('Lança exceção ao enviar email já existente ao editar administrador', function () {
            $this->dao->shouldReceive('obterComRestricoes')->andReturn([ new Administrador(3) ]);
            $this->dao->shouldReceive('existe')->andReturn(true);

            $administrador = new Administrador(2, 'Artur Alves', 'arturalvesdeoliveira28@gmail.com', 12345678);

            try {
                $this->service->salvar($administrador);
            } catch (ServiceException $e) {
                validarErroSalvar($e, 'email', 'Email já pertence a outro administrador.');
            }
        });

        it('Lança exceção ao enviar senha vazia para administrador', function () {
            $this->dao->shouldReceive('obterComRestricoes')->andReturn([]);

            $administrador = new Administrador(0, 'Artur Alves', 'arturalvesdeoliveira28@gmail.com', '');

            try {
                $this->service->salvar($administrador);
            } catch (ServiceException $e) {
                validarErroSalvar($e, 'senha', 'Preencha a senha.');
            }
        });

        it('Lança exceção ao enviar senha com tamanho diferente que o permitido para administrador', function () {
            $this->dao->shouldReceive('obterComRestricoes')->andReturn([]);

            $senhaForaDoTamanhoPermitido = str_repeat('a', AdministradorService::TAMANHO_SENHA + 1);
            $administrador = new Administrador(0, 'Artur Alves', 'arturalvesdeoliveira28@gmail.com', $senhaForaDoTamanhoPermitido);

            try {
                $this->service->salvar($administrador);
            } catch (ServiceException $e) {
                validarErroSalvar($e, 'senha', 'A senha deve ter ' . AdministradorService::TAMANHO_SENHA . ' caracteres.');
            }
        });
    });

    describe('Autenticar', function () {
        it('Lança exceção quando administrador não é encontrado', function () {
            $this->dao->shouldReceive('obterComRestricoes')->andReturn([]);

            expect(function () {
                $this->service->autenticar('artur@gmail', 'aaa');
            })->toThrow(new NaoAutorizadoException('Email ou senha inválidos.'));
        });

        it('Lança exceção quando email ou senha são inválidos', function () {
            $administrador = new Administrador(1, 'Artur', 'artur@gmail', '12345678');
            $this->dao->shouldReceive('obterComRestricoes')->andReturn([ $administrador ]);

            expect(function () {
                $this->service->autenticar('artur@gmail', 'aaa');
            })->toThrow(new NaoAutorizadoException('Email ou senha inválidos.'));
        });

        it('Lança exceção ao gerar token inválido', function () {
            $administrador = new Administrador(1, 'Artur', 'artur@gmail', '12345678');
            $this->dao->shouldReceive('obterComRestricoes')->andReturn([ $administrador ]);

            allow($this->service)->toReceive('verificarSenha')->andReturn(true);
            allow($this->service)->toReceive('gerarToken')->andReturn(null);

            expect(function () {
                $this->service->autenticar('artur@gmail', '12345678');
            })->toThrow(new Exception('Houve um erro ao gerar o token de acesso.'));
        });

        it('Autentica administrador corretamente gerando o token', function () {
            $administrador = new Administrador(1, 'Artur', 'artur@gmail', '12345678');
            $this->dao->shouldReceive('obterComRestricoes')->andReturn([ $administrador ]);

            allow($this->service)->toReceive('verificarSenha')->andReturn(true);
            allow($this->service)->toReceive('gerarToken')->andReturn(new TokenJWT('aaaaa', 3600));

            $token = $this->service->autenticar('artur@gmail', '12345678');
            expect($token)->toBeAnInstanceOf(TokenJWT::class);
        });
    });

    describe('SalvarPermissoes', function () {
        it('Lança exceção quando administrador não é encontrado', function () {
            $this->dao->shouldReceive('obterComId')->andReturn([]);

            expect(function () {
                $this->service->salvarPermissoes([], 2);
            })->toThrow(new NaoEncontradoException('Recurso não encontrado.'));
        });

        it('Lança exceção ao tentar cadastrar permissões do administrador master', function () {
            $idAdministrador = AdministradorService::ID_ADMINISTRADOR_MASTER;
            $this->dao->shouldReceive('obterComId')->andReturn(new Administrador($idAdministrador));

            try {
                $this->service->salvarPermissoes([], $idAdministrador);
            } catch (ServiceException $e) {
                validarErroSalvar($e, 'permissoes', 'Não é permitido alterar as permissões do administrador master.');
            }
        });

        it('Lança exceção ao tentar cadastrar permissões inválidas', function () {
            $idAdministrador = 2;
            $this->dao->shouldReceive('obterComId')->andReturn(new Administrador($idAdministrador));
            $this->dao->shouldReceive('obterIdsPermissao')->andReturn([]);

            try {
                $this->service->salvarPermissoes([ 'Permissão inválida' ], $idAdministrador);
            } catch (ServiceException $e) {
                validarErroSalvar($e, 'permissoes', 'Nenhuma permissão enviada é válida.');
            }
        });

        it('Salva permissões do administrador', function () {
            $idAdministrador = 2;
            $this->dao->shouldReceive('obterComId')->andReturn(new Administrador($idAdministrador));
            $this->dao->shouldReceive('obterIdsPermissao')->andReturn([ 1,2 ]);
            $this->dao->shouldReceive('limparPermissoes')->andReturn(true);
            $this->dao->shouldReceive('salvarPermissoes')->andReturn(true);

            expect(function () use ($idAdministrador) {
                $this->service->salvarPermissoes([ 'Permissão inválida' ], $idAdministrador);
            })->not->toThrow();
        });
    });

    describe('excluirComId', function () {
        it('Lança exceção ao tentar excluir administrador master', function () {
            $idAdministrador = AdministradorService::ID_ADMINISTRADOR_MASTER;
            $this->dao->shouldReceive('obterComId')->andReturn(new Administrador($idAdministrador));

            try {
                $this->service->excluirComId($idAdministrador);
            } catch (ServiceException $e) {
                validarErroSalvar($e, 'administrador', 'Não é possível excluir o administrador master.');
            }
        });

        it('Exclui administrador', function () {
            $idAdministrador = 2;
            $this->dao->shouldReceive('obterComId')->andReturn(new Administrador($idAdministrador));
            $this->dao->shouldReceive('existe')->andReturn(true);
            $this->dao->shouldReceive('excluirComId')->andReturn(true);

            expect(function () use ($idAdministrador) {
                $this->service->excluirComId($idAdministrador);
            })->not->toThrow();
        });
    });
});
