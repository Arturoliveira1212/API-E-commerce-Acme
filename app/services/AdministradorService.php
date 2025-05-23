<?php

namespace app\services;

use Exception;
use app\services\Service;
use app\classes\jwt\TokenJWT;
use app\classes\Administrador;
use app\classes\enum\OperacaoObjeto;
use app\classes\utils\Validador;
use app\dao\AdministradorDAO;
use app\exceptions\NaoAutorizadoException;
use app\exceptions\NaoEncontradoException;
use app\exceptions\ServiceException;
use app\traits\Autenticavel;
use app\traits\Criptografavel;

class AdministradorService extends Service
{
    use Criptografavel;
    use Autenticavel;

    public const ID_ADMINISTRADOR_MASTER = 1;
    public const TAMANHO_MINIMO_NOME = 2;
    public const TAMANHO_MAXIMO_NOME = 100;
    public const TAMANHO_MINIMO_EMAIL = 5;
    public const TAMANHO_MAXIMO_EMAIL = 200;
    public const TAMANHO_SENHA = 8;

    protected function preSalvar($administrador, int $operacaoObjeto, ?int $idRecursoPai = null)
    {
        parent::preSalvar($administrador, $operacaoObjeto, $idRecursoPai);

        $senha = $administrador->getSenha();
        $senhaCriptografada = $this->gerarHash($senha);
        $administrador->setSenha($senhaCriptografada);
    }

    protected function validar($administrador, int $operacaoObjeto, array &$erro = [])
    {
        if ($this->administradorEhMaster($administrador)) {
            $erro['administrador'] = 'Não é possível editar o administrador master.';
        } else {
            $this->validarNome($administrador, $erro);
            $this->validarEmail($administrador, $operacaoObjeto, $erro);
            $this->validarSenha($administrador, $erro);
        }
    }

    private function validarNome(Administrador $administrador, array &$erro)
    {
        $validacaoTamanhoNome = Validador::validarTamanhoTexto($administrador->getNome(), self::TAMANHO_MINIMO_NOME, self::TAMANHO_MAXIMO_NOME);
        if ($validacaoTamanhoNome == 0) {
            $erro['nome'] = 'Preencha o nome.';
        } elseif ($validacaoTamanhoNome == -1) {
            $erro['nome'] = 'O nome deve ter entre ' . self::TAMANHO_MINIMO_NOME . ' e ' . self::TAMANHO_MAXIMO_NOME . ' caracteres.';
        }
    }

    private function validarEmail(Administrador $administrador, int $operacaoObjeto, array &$erro)
    {
        $validacaoTamanhoEmail = Validador::validarTamanhoTexto($administrador->getEmail(), self::TAMANHO_MINIMO_EMAIL, self::TAMANHO_MAXIMO_EMAIL);
        if ($validacaoTamanhoEmail == 0) {
            $erro['email'] = 'Preencha o email.';
        } elseif ($validacaoTamanhoEmail == -1) {
            $erro['email'] = 'O email deve ter entre ' . self::TAMANHO_MINIMO_EMAIL . ' e ' . self::TAMANHO_MAXIMO_EMAIL . ' caracteres.';
        } elseif (! Validador::validarEmail($administrador->getEmail())) {
            $erro['email'] = 'Email inválido.';
        } elseif ($this->emailPertenceAOutroAdministrador($administrador, $operacaoObjeto)) {
            $erro['email'] = 'Email já pertence a outro administrador.';
        }
    }

    private function emailPertenceAOutroAdministrador(Administrador $administrador, int $operacaoObjeto)
    {
        $administradorCadastrado = $this->obterComEmail($administrador->getEmail());
        $existeAdministrador = $administradorCadastrado instanceof Administrador;

        if ($existeAdministrador && $operacaoObjeto == OperacaoObjeto::CADASTRAR) {
            return true;
        }

        if ($existeAdministrador && $operacaoObjeto == OperacaoObjeto::EDITAR && $administrador->getId() != $administradorCadastrado->getId()) {
            return true;
        }

        return false;
    }

    private function validarSenha(Administrador $administrador, array &$erro)
    {
        $validacaoTamanhoSenha = Validador::validarTamanhoTexto($administrador->getSenha(), self::TAMANHO_SENHA, self::TAMANHO_SENHA);
        if ($validacaoTamanhoSenha == 0) {
            $erro['senha'] = 'Preencha a senha.';
        } elseif ($validacaoTamanhoSenha == -1) {
            $erro['senha'] = 'A senha deve ter ' . self::TAMANHO_SENHA . ' caracteres.';
        }
    }

    private function administradorEhMaster(Administrador $administrador)
    {
        return $administrador->getId() == self::ID_ADMINISTRADOR_MASTER;
    }

    public function excluirComId(int $id)
    {
        $administrador = $this->obterComId($id);
        if ($this->administradorEhMaster($administrador)) {
            $erro['administrador'] = 'Não é possível excluir o administrador master.';
            throw new ServiceException(json_encode($erro));
        }

        return parent::excluirComId($id);
    }

    public function autenticar(string $email, string $senha)
    {
        $administrador = $this->obterComEmail($email);
        if (! $administrador instanceof Administrador || ! $this->verificarSenha($senha, $administrador->getSenha())) {
            throw new NaoAutorizadoException('Email ou senha inválidos.');
        }

        $tokenJWT = $this->gerarToken(
            $administrador->getId(),
            $administrador->getNome(),
            'admin'
        );

        if (! $tokenJWT instanceof TokenJWT) {
            throw new Exception('Houve um erro ao gerar o token de acesso.');
        }

        return $tokenJWT;
    }

    public function obterComEmail(string $email)
    {
        $restricoes = [ 'email' => $email ];
        $administradores = $this->obterComRestricoes($restricoes);

        return array_shift($administradores);
    }

    public function salvarPermissoes(array $permissoes, int $idAdministrador)
    {
        $administrador = $this->obterComId($idAdministrador);
        if (! $administrador instanceof Administrador) {
            throw new NaoEncontradoException('Administrador não encontrado.');
        }

        $erro = [];
        $this->validarPermissoes($administrador, $permissoes, $erro);
        if (! empty($erro)) {
            throw new ServiceException(json_encode($erro));
        }

        /** @var AdministradorDAO */
        $administradorDAO = $this->getDao();
        $administradorDAO->limparPermissoes($administrador);

        if (! empty($permissoes)) {
            $idsPermissao = $administradorDAO->obterIdsPermissao($permissoes);
            $administradorDAO->salvarPermissoes($administrador, $idsPermissao);
        }
    }

    private function validarPermissoes(Administrador $administrador, array $permissoes, array &$erro = [])
    {
        if ($this->administradorEhMaster($administrador)) {
            $erro['permissoes'] = 'Não é permitido alterar as permissões do administrador master.';
        } elseif (! empty($permissoes)) {
            /** @var AdministradorDAO */
            $administradorDAO = $this->getDao();
            $idsPermissao = $administradorDAO->obterIdsPermissao($permissoes);
            if (empty($idsPermissao)) {
                $erro['permissoes'] = 'Nenhuma permissão enviada é válida.';
            }
        }
    }
}
