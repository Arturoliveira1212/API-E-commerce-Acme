<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class SemeadorPermissao extends AbstractSeed {

    public function getDependencies(): array {
        return [];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO permissao ( id, descricao ) VALUES
                ( 1, 'Cadastrar Administrador' ),
                ( 2, 'Editar Administrador' ),
                ( 3, 'Visualizar Administrador' ),
                ( 4, 'Excluir Administrador' ),
                ( 5, 'Adicionar Permissão para Administrador' );
        SQL;
        $this->execute( $sql );
    }
}