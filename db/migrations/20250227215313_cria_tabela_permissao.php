<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CriaTabelaPermissao extends AbstractMigration {

    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE permissao (
                id INT PRIMARY KEY AUTO_INCREMENT,
                descricao VARCHAR(100) NOT NULL,
                ativo TINYINT(1) DEFAULT 1
            ) ENGINE=INNODB;
        SQL;
        $this->execute( $sql );
    }

    public function down(): void {
        $this->execute( 'DROP TABLE permissao' );
    }
}
