<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CriaTabelaCategoria extends AbstractMigration {

    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE categoria (
                id INT PRIMARY KEY AUTO_INCREMENT,
                nome VARCHAR(100) NOT NULL UNIQUE,
                descricao VARCHAR(500) NOT NULL,
                ativo TINYINT(1) DEFAULT 1
            ) ENGINE=INNODB;
        SQL;
        $this->execute( $sql );
    }

    public function down(): void {
        $this->execute( 'DROP TABLE categoria' );
    }
}