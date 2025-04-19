<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class SemeadorPermissaoAdministrador extends AbstractSeed
{
    public function getDependencies(): array
    {
        return [];
    }

    public function run(): void
    {
        $this->execute('DELETE FROM permissao_administrador');

        $ps = $this->query('SELECT id FROM permissao WHERE ativo = 1');
        $permissoes = $ps->fetchAll(PDO::FETCH_ASSOC);

        if (! empty($permissoes)) {
            foreach ($permissoes as $permissao) {
                $sql = <<<SQL
                    INSERT INTO permissao_administrador ( idAdministrador, idPermissao ) VALUES
                        ( :administrador, :permissao );
                SQL;
                $this->execute($sql, [
                    'administrador' => 1,
                    'permissao' => $permissao['id']
                ]);
            }
        }
    }
}
