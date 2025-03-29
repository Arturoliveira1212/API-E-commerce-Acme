<?php

declare(strict_types=1);

use app\traits\Criptografavel;
use Phinx\Seed\AbstractSeed;

class SemeadorAdministrador extends AbstractSeed {
    use Criptografavel;

    public function getDependencies(): array {
        return [];
    }

    public function run(): void {
        $senhaCriptografada = $this->gerarHash( '12345678' );
        $sql = <<<SQL
            DELETE FROM administrador;
            INSERT INTO administrador (id, nome, email, senha) VALUES
                (1, 'Admin Master', 'admin@gmail.com', '$senhaCriptografada');
        SQL;
        $this->execute( $sql );
    }
}