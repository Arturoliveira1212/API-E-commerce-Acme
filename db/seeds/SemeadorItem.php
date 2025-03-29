<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class SemeadorItem extends AbstractSeed {

    public function getDependencies(): array {
        return [ 'SemeadorProduto' ];
    }

    public function run(): void {
        $sql = <<<SQL
            DELETE FROM item;
            INSERT INTO item (id, idProduto, sku, tamanho, estoque, pesoEmGramas) VALUES
                (1, 1, 'SKU00001', 'U', 0, 180.00),
                (2, 2, 'SKU00002', 'U', 0, 2200.00),
                (3, 3, 'SKU00003', 'U', 0, 250.00),
                (4, 4, 'SKU00004', 'P', 0, 150.00),
                (5, 4, 'SKU00005', 'M', 0, 150.00),
                (6, 4, 'SKU00006', 'G', 0, 150.00),
                (7, 5, 'SKU00008', 'P', 0, 400.00),
                (8, 5, 'SKU00009', 'M', 0, 400.00),
                (9, 5, 'SKU00010', 'G', 0, 400.00),
                (10, 5, 'SKU00011', 'GG', 0, 400.00),
                (11, 6, 'SKU00012', '38', 0, 900.00),
                (12, 6, 'SKU00013', '39', 0, 900.00),
                (13, 6, 'SKU00014', '40', 0, 900.00),
                (14, 7, 'SKU00015', 'U', 0, 500.00),
                (15, 8, 'SKU00016', 'U', 0, 300.00);

        SQL;
        $this->execute( $sql );
    }
}