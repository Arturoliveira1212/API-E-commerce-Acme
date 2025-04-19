<?php

namespace app\classes;

class ItemPedido extends Model
{
    private int $id = 0;
    private ?Item $item = null;
    private int $quantidade = 0;
    private float $valorVenda = 0.0;

    public function emArray(): array
    {
        return [];
    }
}
