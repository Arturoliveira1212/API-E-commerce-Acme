<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CriaTabelaAdministrador extends AbstractMigration {

    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE administrador (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(100) NOT NULL,
                email VARCHAR(200) NOT NULL UNIQUE,
                senha VARCHAR(255) NOT NULL,
                ativo TINYINT(1) DEFAULT 1
            ) ENGINE=INNODB;
        SQL;
        $this->execute( $sql );
    }

    public function down(): void {
        $this->execute( 'DROP TABLE administrador' );
    }
}
