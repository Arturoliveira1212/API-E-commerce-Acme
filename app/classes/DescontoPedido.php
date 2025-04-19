<?php

use app\classes\Model;

class DescontoPedido extends Model
{
    private int $id = 0;
    private int $tipoDesconto = 0;
    private float $valor = 0.0;

    public function emArray(): array
    {
        return [];
    }
}
