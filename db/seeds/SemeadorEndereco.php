<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class SemeadorEndereco extends AbstractSeed
{
    public function getDependencies(): array
    {
        return [ 'SemeadorCliente' ];
    }

    public function run(): void
    {
        $sql = <<<SQL
            DELETE FROM endereco;
            INSERT INTO endereco (id, idCliente, logradouro, cidade, bairro, numero, cep, complemento) VALUES
                (1, 1, 'Rua das Flores', 'São Paulo', 'Centro', '123', '01001000', 'Apto 101'),
                (2, 1, 'Avenida Paulista', 'São Paulo', 'Bela Vista', '200', '01311000', 'Bloco B'),
                (3, 1, 'Rua XV de Novembro', 'Curitiba', 'Centro', '50', '80020010', 'Próximo à praça'),
                (4, 1, 'Rua das Palmeiras', 'Rio de Janeiro', 'Copacabana', '300', '22011001', ''),
                (5, 2, 'Avenida Atlântica', 'Rio de Janeiro', 'Copacabana', '400', '22021001', 'Vista para o mar'),
                (6, 2, 'Rua Sete de Setembro', 'Porto Alegre', 'Centro', '55', '90010001', ''),
                (7, 2, 'Rua do Comércio', 'Belo Horizonte', 'Savassi', '120', '30112000', 'Perto do shopping'),
                (8, 3, 'Alameda das Rosas', 'Goiânia', 'Setor Oeste', '250', '74150020', ''),
                (9, 3, 'Rua Augusta', 'São Paulo', 'Consolação', '789', '01413001', 'Cobertura'),
                (10, 3, 'Avenida Brasil', 'Recife', 'Boa Viagem', '99', '51020001', 'Perto da praia'),
                (11, 3, 'Rua das Laranjeiras', 'Florianópolis', 'Trindade', '33', '88036010', 'Casa azul');

        SQL;
        $this->execute($sql);
    }
}
