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
                ( 3, 'Excluir Administrador' ),
                ( 4, 'Adicionar Permissão para Administrador' ),
                ( 5, 'Cadastrar Categoria' ),
                ( 6, 'Editar Categoria' ),
                ( 7, 'Excluir Categoria' );
        SQL;
        $this->execute( $sql );
    }
}