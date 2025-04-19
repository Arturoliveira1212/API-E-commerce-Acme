<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class SemeadorProduto extends AbstractSeed
{
    public function getDependencies(): array
    {
        return [ 'SemeadorCategoria' ];
    }

    public function run(): void
    {
        $sql = <<<SQL
            DELETE FROM produto;
            INSERT INTO produto (id, nome, referencia, cor, preco, descricao, idCategoria, dataCadastro) VALUES
                (1, 'Smartphone X', 'SMP001', 'Preto', 1499.90, 'Smartphone com tela AMOLED de 6.5", 128GB de memória', 1, '2025-03-29 10:00:00'),
                (2, 'Notebook Gamer', 'NOT002', 'Preto', 5999.90, 'Notebook gamer, com placa de vídeo NVIDIA e 16GB de RAM', 1, '2025-03-29 11:00:00'),
                (3, 'Fone de Ouvido', 'FON003', 'Cinza', 199.90, 'Fone de ouvido com cancelamento de ruído, ideal para música e chamadas', 1, '2025-03-29 12:00:00'),
                (4, 'Camiseta Branca', 'CAM004', 'Branco', 49.90, 'Camiseta de algodão, modelo básico, para todas as idades', 2, '2025-03-29 13:00:00'),
                (5, 'Calça Jeans', 'CAL005', 'Azul', 89.90, 'Calça jeans, modelo skinny, confortável e moderno', 2, '2025-03-29 14:00:00'),
                (6, 'Tênis Esportivo', 'TEN006', 'Preto', 159.90, 'Tênis esportivo, ideal para corridas e atividades físicas', 2, '2025-03-29 15:00:00'),
                (7, 'Livro de Programação', 'LIV009', 'Capa preta', 89.90, 'Livro sobre fundamentos de programação e algoritmos', 3, '2025-03-29 18:00:00'),
                (8, 'Livro de História', 'LIV010', 'Capa azul', 39.90, 'Livro sobre história do Brasil, edição 2025', 3, '2025-03-29 19:00:00');
        SQL;
        $this->execute($sql);
    }
}
