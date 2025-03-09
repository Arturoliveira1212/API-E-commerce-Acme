<?php

namespace app\services;

use Exception;
use app\classes\Cliente;
use app\services\Service;
use app\traits\Autenticavel;
use app\classes\jwt\TokenJWT;
use app\traits\Criptografavel;
use app\classes\utils\Validador;
use app\dao\BancoDadosRelacional;
use app\exceptions\NaoAutorizadoException;
use DateTime;

class ClienteService extends Service {
    use Criptografavel;
    use Autenticavel;

    const TAMANHO_MINIMO_NOME = 1;
    const TAMANHO_MAXIMO_NOME = 100;
    const TAMANHO_MINIMO_EMAIL = 1;
    const TAMANHO_MAXIMO_EMAIL = 200;
    const TAMANHO_CPF = 14;
    const TAMANHO_SENHA = 8;

    protected function preSalvar( $cliente, ?int $idRecursoPai = null ){
        parent::preSalvar( $cliente );

        $senha = $cliente->getSenha();
        $senhaCriptografada = $this->gerarHash( $senha );
        $cliente->setSenha( $senhaCriptografada );
    }

    protected function validar( $cliente, array &$erro = [] ){
        $this->validarNome( $cliente, $erro );
        $this->validarEmail( $cliente, $erro );
        $this->validarCpf( $cliente, $erro );
        $this->validarSenha( $cliente, $erro );
        $this->validarDataNascimento( $cliente, $erro );
    }

    private function validarNome( Cliente $cliente, array &$erro ){
        $validacaoTamanhoNome = Validador::validarTamanhoTexto( $cliente->getNome(), self::TAMANHO_MINIMO_NOME, self::TAMANHO_MAXIMO_NOME );
        if( $validacaoTamanhoNome == 0 ){
            $erro['nome'] = 'Preencha o nome.';
        } else if( $validacaoTamanhoNome == -1 ){
            $erro['nome'] = 'O nome deve ter entre ' . self::TAMANHO_MINIMO_NOME . ' e ' . self::TAMANHO_MAXIMO_NOME . ' caracteres.';
        }
    }

    private function validarEmail( Cliente $cliente, array &$erro ){
        $validacaoTamanhoEmail = Validador::validarTamanhoTexto( $cliente->getEmail(), self::TAMANHO_MINIMO_EMAIL, self::TAMANHO_MAXIMO_EMAIL );
        if( $validacaoTamanhoEmail == 0 ){
            $erro['email'] = 'Preencha o email.';
        } else if( $validacaoTamanhoEmail == -1 ){
            $erro['email'] = 'O email deve ter entre ' . self::TAMANHO_MINIMO_EMAIL . ' e ' . self::TAMANHO_MAXIMO_EMAIL . ' caracteres.';
        } else if( ! Validador::validarEmail( $cliente->getEmail() ) ){
            $erro['email'] = 'Email inválido.';
        } else if( $this->emailPertenceAOutroCliente( $cliente ) ) {
            $erro['email'] = 'Email já pertence a outro cliente.';
        }
    }

    private function emailPertenceAOutroCliente( Cliente $cliente ){
        $clienteCadastrado = $this->obterComEmail( $cliente->getEmail() );
        $existeAdministrador = $clienteCadastrado instanceof Cliente;

        if( $existeAdministrador && $cliente->getId() == BancoDadosRelacional::ID_INEXISTENTE ){
            return true;
        }

        if( $existeAdministrador && $cliente->getId() != BancoDadosRelacional::ID_INEXISTENTE && $cliente->getId() != $clienteCadastrado->getId() ){
            return true;
        }

        return false;
    }

    private function validarCpf( Cliente $cliente, array &$erro ){
        if( ! Validador::validarCpf( $cliente->getCpf() ) ){
            $erro['cpf'] = 'CPF inválido. O formato esperado é 123.456.789-09.';
        } else if( $this->cpfPertenceAOutroCliente( $cliente ) ){
            $erro['cpf'] = 'CPF já pertence a outro cliente.';
        }
    }

    private function cpfPertenceAOutroCliente( Cliente $cliente ){
        $clienteCadastrado = $this->obterComCpf( $cliente->getCpf() );
        $existeAdministrador = $clienteCadastrado instanceof Cliente;

        if( $existeAdministrador && $cliente->getId() == BancoDadosRelacional::ID_INEXISTENTE ){
            return true;
        }

        if( $existeAdministrador && $cliente->getId() != BancoDadosRelacional::ID_INEXISTENTE && $cliente->getId() != $clienteCadastrado->getId() ){
            return true;
        }

        return false;
    }

    private function validarSenha( Cliente $cliente, array &$erro ){
        $validacaoTamanhoSenha = Validador::validarTamanhoTexto( $cliente->getSenha(), self::TAMANHO_SENHA, self::TAMANHO_SENHA );
        if( $validacaoTamanhoSenha == 0 ){
            $erro['senha'] = 'Preencha a senha.';
        } else if( $validacaoTamanhoSenha == -1 ){
            $erro['senha'] = 'A senha deve ter ' . self::TAMANHO_SENHA . ' caracteres.';
        }
    }

    private function validarDataNascimento( Cliente $cliente, array &$erro ){
        if( ! $cliente->getDataNascimento() instanceof DateTime ){
            $erro['dataNascimento'] = 'Data de nascimento inválido. O formato esperado é dd/mm/yyyy.';
        } else if( $cliente->getDataNascimento() > new DateTime() ){
            $erro['dataNascimento'] = 'A data de nascimento precisa ser menor que a data atual.';
        }
    }

    public function autenticar( string $email, string $senha ){
        $cliente = $this->obterComEmail( $email );
        if( ! $cliente instanceof Cliente || ! $this->verificarSenha( $senha, $cliente->getSenha() ) ){
            throw new NaoAutorizadoException( 'Email ou senha inválidos.' );
        }

        $tokenJWT = $this->gerarToken(
            $cliente->getId(),
            $cliente->getNome(),
            'cliente'
        );

        if( ! $tokenJWT instanceof TokenJWT ){
            throw new Exception( 'Houve um erro ao gerar o token de acesso.' );
        }

        return $tokenJWT;
    }

    public function obterComEmail( string $email ){
        $restricoes = [ 'email' => $email ];
        $clientes = $this->obterComRestricoes( $restricoes );

        return array_shift( $clientes );
    }

    public function obterComCpf( string $cpf ){
        $restricoes = [ 'cpf' => $cpf ];
        $clientes = $this->obterComRestricoes( $restricoes );

        return array_shift( $clientes );
    }
}