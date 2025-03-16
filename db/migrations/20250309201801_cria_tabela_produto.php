<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CriaTabelaProduto extends AbstractMigration {

    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE produto (
                id INT PRIMARY KEY AUTO_INCREMENT,
                nome VARCHAR(100) NOT NULL UNIQUE,
                referencia VARCHAR(10) NOT NULL UNIQUE,
                cor VARCHAR(50) NOT NULL,
                preco DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                descricao VARCHAR(300) NOT NULL,
                idCategoria INT NOT NULL,
                dataCadastro DATETIME NOT NULL,
                ativo TINYINT(1) DEFAULT 1,
                CONSTRAINT fk__id_categoria FOREIGN KEY (idCategoria) REFERENCES categoria(id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute( $sql );
    }

    public function down(): void {
        $this->execute( 'DROP TABLE produto' );
    }
}