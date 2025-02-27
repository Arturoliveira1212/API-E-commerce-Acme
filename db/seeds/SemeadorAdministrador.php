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
            INSERT INTO administrador ( id, nome, email, senha ) VALUES
                ( :id, :nome, :email, :senha );
        SQL;
        $this->execute( $sql, [
            'id' => 1,
            'nome' => 'Admin Master',
            'email' => 'admin@gmail.com',
            'senha' => $senhaCriptografada
        ] );
    }
}