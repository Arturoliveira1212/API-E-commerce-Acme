<?php

namespace app\dao;

use app\classes\Model;

interface DAO
{
    public function salvar(Model $objeto, int $operacaoObjeto, ?int $idRecursoPai = null);
    public function desativarComId(int $id);
    public function excluirComId(int $id);
    public function existe(string $campo, string $valor);
    public function obterComId(int $id);
    public function obterComRestricoes(array $restricoes);
}
