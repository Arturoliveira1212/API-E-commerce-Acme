<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CriaTabelaItem extends AbstractMigration
{
    public function up(): void
    {
        $sql = <<<'SQL'
            CREATE TABLE item (
                id INT PRIMARY KEY AUTO_INCREMENT,
                idProduto INT NOT NULL,
                sku VARCHAR(8) NOT NULL,
                tamanho VARCHAR(4) NOT NULL,
                estoque INT NOT NULL,
                pesoEmGramas DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                ativo TINYINT(1) DEFAULT 1,
                CONSTRAINT fk__id_produto FOREIGN KEY (idProduto) REFERENCES produto(id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void
    {
        $this->execute('DROP TABLE item');
    }
}
