<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class SemeadorPermissao extends AbstractSeed {

    public function getDependencies(): array {
        return [];
    }

    public function run(): void {
        $sql = <<<SQL
            DELETE FROM permissao;
            INSERT INTO permissao ( id, descricao ) VALUES
                (1, 'Cadastrar Administrador'),
                (2, 'Editar Administrador'),
                (3, 'Excluir Administrador'),
                (4, 'Adicionar Permissão para Administrador'),
                (5, 'Cadastrar Categoria'),
                (6, 'Editar Categoria'),
                (7, 'Excluir Categoria'),
                (8, 'Cadastrar Cliente'),
                (9, 'Editar Cliente'),
                (10, 'Excluir Cliente'),
                (11, 'Cadastrar Endereço'),
                (12, 'Editar Endereço'),
                (13, 'Excluir Endereço'),
                (14, 'Cadastrar Produto'),
                (15, 'Editar Produto'),
                (16, 'Excluir Produto'),
                (17, 'Cadastrar Item'),
                (18, 'Editar Item'),
                (19, 'Excluir Item'),
                (20, 'Movimentar Estoque Item');
        SQL;
        $this->execute( $sql );
    }
}