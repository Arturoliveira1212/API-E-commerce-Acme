<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CriaTabelaPermissaoAdministrador extends AbstractMigration
{
    public function up(): void
    {
        $sql = <<<'SQL'
            CREATE TABLE permissao_administrador (
                id INT PRIMARY KEY AUTO_INCREMENT,
                idAdministrador INT NOT NULL,
                idPermissao INT NOT NULL,
                ativo TINYINT(1) DEFAULT 1,
                CONSTRAINT fk__id_administrador FOREIGN KEY (idAdministrador) REFERENCES administrador(id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk__id_permissao FOREIGN KEY (idPermissao) REFERENCES permissao(id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=INNODB;
        SQL;
        $this->execute($sql);
    }

    public function down(): void
    {
        $this->execute('DROP TABLE permissao_administrador');
    }
}
