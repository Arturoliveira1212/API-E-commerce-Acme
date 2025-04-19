<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class SemeadorCategoria extends AbstractSeed
{
    public function getDependencies(): array
    {
        return [];
    }

    public function run(): void
    {
        $sql = <<<SQL
            DELETE FROM categoria;

            INSERT INTO categoria (id, nome, descricao, ativo) VALUES
                (1, 'Eletrônicos', 'Produtos eletrônicos como celulares, notebooks e acessórios.', 1),
                (2, 'Roupas', 'Vestuário masculino, feminino e infantil.', 1),
                (3, 'Livros', 'Livros e materiais de estudo.', 1);
        SQL;
        $this->execute($sql);
    }
}
