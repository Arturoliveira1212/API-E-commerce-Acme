<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CriaTabelaEndereco extends AbstractMigration {

    public function up(): void {
        $sql = <<<'SQL'
            CREATE TABLE endereco (
                id INT PRIMARY KEY AUTO_INCREMENT,
                idCliente INT NOT NULL,
                logradouro VARCHAR(100) NOT NULL,
                cidade VARCHAR(100) NOT NULL,
                bairro VARCHAR(100) NOT NULL,
                numero VARCHAR(10) NOT NULL,
                cep VARCHAR(8) NOT NULL,
                complemento VARCHAR(100) NOT NULL,
                ativo TINYINT(1) DEFAULT 1,
                CONSTRAINT fk__id_cliente FOREIGN KEY (idCliente) REFERENCES cliente(id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute( $sql );
    }

    public function down(): void {
        $this->execute( 'DROP TABLE endereco' );
    }
}
