<?php

namespace app\services;

use Exception;
use app\services\Service;
use app\classes\jwt\TokenJWT;
use app\classes\Administrador;
use app\classes\utils\Validador;
use app\classes\jwt\AutenticacaoJWT;
use app\classes\jwt\PayloadJWT;
use app\classes\Model;
use app\databases\BancoDadosRelacional;
use app\exceptions\NaoAutorizadoException;
use app\traits\Criptografavel;

class AdministradorService extends Service {
    use Criptografavel;

    const TAMANHO_MINIMO_NOME = 1;
    const TAMANHO_MAXIMO_NOME = 100;
    const TAMANHO_MINIMO_EMAIL = 1;
    const TAMANHO_MAXIMO_EMAIL = 200;
    const TAMANHO_SENHA = 8;

    protected function preSalvar( $administrador ){
        parent::preSalvar( $administrador );

        $senha = $administrador->getSenha();
        $senhaCriptografada = $this->gerarHash( $senha );
        $administrador->setSenha( $senhaCriptografada );
    }

    protected function validar( $administrador, array &$erro = [] ){
        $this->validarNome( $administrador, $erro );
        $this->validarEmail( $administrador, $erro );
        $this->validarSenha( $administrador, $erro );
    }

    private function validarNome( Administrador $administrador, array &$erro ){
        $validacaoTamanhoNome = Validador::validarTamanhoTexto( $administrador->getNome(), self::TAMANHO_MINIMO_NOME, self::TAMANHO_MAXIMO_NOME );
        if( $validacaoTamanhoNome == 0 ){
            $erro['nome'] = 'Preencha o nome.';
        } else if( $validacaoTamanhoNome == -1 ){
            $erro['nome'] = 'O nome deve ter entre ' . self::TAMANHO_MINIMO_NOME . ' e ' . self::TAMANHO_MAXIMO_NOME . ' caracteres.';
        }
    }

    private function validarEmail( Administrador $administrador, array &$erro ){
        $validacaoTamanhoEmail = Validador::validarTamanhoTexto( $administrador->getEmail(), self::TAMANHO_MINIMO_EMAIL, self::TAMANHO_MAXIMO_EMAIL );
        if( $validacaoTamanhoEmail == 0 ){
            $erro['email'] = 'Preencha o email.';
        } else if( $validacaoTamanhoEmail == -1 ){
            $erro['email'] = 'O email deve ter entre ' . self::TAMANHO_MINIMO_EMAIL . ' e ' . self::TAMANHO_MAXIMO_EMAIL . ' caracteres.';
        } else if( ! Validador::validarEmail( $administrador->getEmail() ) ){
            $erro['email'] = 'Email inválido.';
        } else if( $administrador->getId() == BancoDadosRelacional::ID_INEXISTENTE && $this->getDao()->existe( 'email', $administrador->getEmail() ) ){
            $erro['email'] = 'Email já pertence a um administrador.';
        }
    }

    private function validarSenha( Administrador $administrador, array &$erro ){
        $validacaoTamanhoSenha = Validador::validarTamanhoTexto( $administrador->getSenha(), self::TAMANHO_SENHA, self::TAMANHO_SENHA );
        if( $validacaoTamanhoSenha == 0 ){
            $erro['senha'] = 'Preencha a senha.';
        } else if( $validacaoTamanhoSenha == -1 ){
            $erro['senha'] = 'A senha deve ter ' . self::TAMANHO_SENHA . ' caracteres.';
        }
    }

    public function autenticar( string $email, string $senha ){
        $administrador = $this->obterComEmail( $email );
        if( ! $administrador instanceof Administrador ){
            throw new NaoAutorizadoException( 'Email não encontrado.' );
        }

        if( ! $this->verificarSenha( $senha, $administrador->getSenha() ) ){
            throw new NaoAutorizadoException( 'Email ou senha inválidos.' );
        }

        $autenticacaoJWT = new AutenticacaoJWT();
        $tokenJWT = $autenticacaoJWT->gerarToken(
            $administrador->getId(),
            $administrador->getNome(),
            'admin'
        );

        if( ! $tokenJWT instanceof TokenJWT ){
            throw new Exception( 'Houve um erro ao gerar o token de acesso.' );
        }

        return $tokenJWT;
    }

    public function obterComEmail( string $email ){
        $restricoes = [ 'email' => $email ];
        $administradores = $this->obterComRestricoes( $restricoes );

        return array_shift( $administradores );
    }
}