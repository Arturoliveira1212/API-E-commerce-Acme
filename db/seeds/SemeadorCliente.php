<?php

declare(strict_types=1);

use app\traits\Criptografavel;
use Phinx\Seed\AbstractSeed;

class SemeadorCliente extends AbstractSeed
{
    use Criptografavel;

    public function getDependencies(): array
    {
        return [];
    }

    public function run(): void
    {
        $senhaCriptografada = $this->gerarHash('12345678');
        $sql = <<<SQL
            DELETE FROM cliente;
            INSERT INTO cliente ( id, nome, email, cpf, senha, dataNascimento ) VALUES
                (1, 'João da Silva', 'joao@email.com', '304.040.310-99', '$senhaCriptografada', '1990-05-15'),
                (2, 'Maria Oliveira', 'maria@email.com', '496.146.390-67', '$senhaCriptografada', '1985-08-22'),
                (3, 'Carlos Santos', 'carlos@email.com', '885.944.500-01', '$senhaCriptografada', '1995-12-10');
        SQL;
        $this->execute($sql);
    }
}
