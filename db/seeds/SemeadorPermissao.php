<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class SemeadorPermissao extends AbstractSeed {

    public function getDependencies(): array {
        return [];
    }

    public function run(): void {
        $sql = <<<SQL
            INSERT INTO permissao ( descricao ) VALUES
                ( 'Cadastrar Administrador' ),
                ( 'Editar Administrador' ),
                ( 'Excluir Administrador' ),
                ( 'Adicionar Permissão para Administrador' ),
                ( 'Cadastrar Categoria' ),
                ( 'Editar Categoria' ),
                ( 'Excluir Categoria' ),
                ( 'Cadastrar Cliente' ),
                ( 'Editar Cliente' ),
                (  'Excluir Cliente' ),
                (  'Cadastrar Endereço' ),
                (  'Editar Endereço' ),
                (  'Excluir Endereço' ),
                (  'Cadastrar Produto' ),
                (  'Editar Produto' ),
                (  'Excluir Produto' ),
                (  'Cadastrar Item' ),
                (  'Editar Item' ),
                (  'Excluir Item' ),
                (  'Movimentar Estoque Item' );
        SQL;
        $this->execute( $sql );
    }
}