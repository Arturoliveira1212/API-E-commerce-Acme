<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CriaTabelaMovimentacaoEstoqueItem extends AbstractMigration {

    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE movimentacao_estoque_item (
                id INT PRIMARY KEY AUTO_INCREMENT,
                idItem INT NOT NULL,
                operacao INT NOT NULL,
                quantidade INT NOT NULL,
                ativo TINYINT(1) DEFAULT 1,
                CONSTRAINT fk__id_item FOREIGN KEY (idItem) REFERENCES item(id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute( $sql );
    }

    public function down(): void {
        $this->execute( 'DROP TABLE movimentacao_estoque_item' );
    }
}
