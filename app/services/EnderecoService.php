<?php

namespace app\services;

use app\classes\Cliente;
use app\classes\Endereco;
use app\classes\enum\OperacaoObjeto;
use app\services\Service;
use app\classes\utils\Validador;
use app\classes\factory\ClassFactory;
use app\dao\EnderecoDAO;
use app\exceptions\NaoEncontradoException;

class EnderecoService extends Service
{
    public const TAMANHO_MINIMO_LOGRADOURO = 5;
    public const TAMANHO_MAXIMO_LOGRADOURO = 100;
    public const TAMANHO_MINIMO_CIDADE = 3;
    public const TAMANHO_MAXIMO_CIDADE = 50;
    public const TAMANHO_MAXIMO_NUMERO = 10;
    public const TAMANHO_CEP = 8;
    public const TAMANHO_MAXIMO_COMPLEMENTO = 100;

    protected function preSalvar($endereco, int $operacaoObjeto, ?int $idRecursoPai = null)
    {
        if ($operacaoObjeto == OperacaoObjeto::CADASTRAR && ! $this->clienteDoEnderecoExiste($idRecursoPai)) {
            throw new NaoEncontradoException('Recurso não encontrado.');
        }

        parent::preSalvar($endereco, $operacaoObjeto, $idRecursoPai);
    }

    private function clienteDoEnderecoExiste(?int $idCliente = null)
    {
        $clienteService = ClassFactory::makeService(Cliente::class);
        $existe = $clienteService->existe('id', $idCliente);

        return $existe;
    }

    protected function validar($endereco, int $operacaoObjeto, array &$erro = [])
    {
        $this->validarLogradouro($endereco, $erro);
        $this->validarCidade($endereco, $erro);
        $this->validarNumero($endereco, $erro);
        $this->validarCep($endereco, $erro);
        $this->validarComplemento($endereco, $erro);
    }

    private function validarLogradouro(Endereco $endereco, array &$erro = [])
    {
        $validacaoTamanhoLogradouro = Validador::validarTamanhoTexto($endereco->getLogradouro(), self::TAMANHO_MINIMO_LOGRADOURO, self::TAMANHO_MAXIMO_LOGRADOURO);
        if ($validacaoTamanhoLogradouro == 0) {
            $erro['logradouro'] = 'Preencha o logradouro.';
        } elseif ($validacaoTamanhoLogradouro == -1) {
            $erro['logradouro'] = 'O logradouro deve ter entre ' . self::TAMANHO_MINIMO_LOGRADOURO . ' e ' . self::TAMANHO_MAXIMO_LOGRADOURO . ' caracteres.';
        }
    }

    private function validarCidade(Endereco $endereco, array &$erro = [])
    {
        $validacaoTamanhoCidade = Validador::validarTamanhoTexto($endereco->getCidade(), self::TAMANHO_MINIMO_CIDADE, self::TAMANHO_MAXIMO_CIDADE);
        if ($validacaoTamanhoCidade == 0) {
            $erro['cidade'] = 'Preencha a cidade.';
        } elseif ($validacaoTamanhoCidade == -1) {
            $erro['cidade'] = 'A cidade deve ter entre ' . self::TAMANHO_MINIMO_CIDADE . ' e ' . self::TAMANHO_MAXIMO_CIDADE . ' caracteres.';
        }
    }

    private function validarNumero(Endereco $endereco, array &$erro = [])
    {
        $validacaoTamanhoNumero = Validador::validarTamanhoTexto($endereco->getNumero(), 0, self::TAMANHO_MAXIMO_NUMERO);
        if ($validacaoTamanhoNumero == -1) {
            $erro['numero'] = 'O número deve ter no máximo ' . self::TAMANHO_MAXIMO_NUMERO . ' caracteres.';
        }
    }

    private function validarCep(Endereco $endereco, array &$erro = [])
    {
        $validacaoTamanhoCep = Validador::validarTamanhoTexto($endereco->getCep(), self::TAMANHO_CEP, self::TAMANHO_CEP);
        if ($validacaoTamanhoCep == 0) {
            $erro['cep'] = 'Preencha o cep.';
        } elseif ($validacaoTamanhoCep == -1) {
            $erro['cep'] = 'O cep deve ter ' . self::TAMANHO_CEP . ' caracteres.';
        }
    }

    private function validarComplemento(Endereco $endereco, array &$erro = [])
    {
        $validacaoTamanhoComplemento = Validador::validarTamanhoTexto($endereco->getComplemento(), 0, self::TAMANHO_MAXIMO_COMPLEMENTO);
        if ($validacaoTamanhoComplemento == -1) {
            $erro['complemento'] = 'O complemento deve ter no máximo ' . self::TAMANHO_MAXIMO_COMPLEMENTO . ' caracteres.';
        }
    }

    public function obterEnderecosDoCliente(int $idCliente)
    {
        if (! $this->clienteDoEnderecoExiste($idCliente)) {
            throw new NaoEncontradoException('Recurso não encontrado.');
        }

        $restricoes = [ 'idCliente' => $idCliente ];
        $enderecos = $this->obterComRestricoes($restricoes);

        return $enderecos;
    }

    public function enderecoPertenceACliente(Cliente $cliente, int $idEndereco)
    {
        /** @var EnderecoDAO */
        $enderecoDAO = $this->getDao();
        $idCliente = $enderecoDAO->obterIdClienteDoEndereco($idEndereco);
        $enderecoPertenceACliente = $idCliente == $cliente->getId();

        return $enderecoPertenceACliente;
    }
}
