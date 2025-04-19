<?php

use app\classes\enum\Enum;

class StatusPedido implements Enum
{
    public const AGUARDANDO_PAGAMENTO = 1;
    public const PAGO = 2;
    public const CANCELADO = 3;
    public const ENTREGUE = 4;

    public static function ehValido($enum)
    {
        return array_key_exists($enum, self::toArray());
    }

    public static function quantidadeOpcoes()
    {
        return count(self::toArray());
    }

    public static function toArray()
    {
        return [
            self::AGUARDANDO_PAGAMENTO => 'AGUARDANDO PAGAMENTO',
            self::PAGO => 'PAGO',
            self::CANCELADO => 'CANCELADO',
            self::ENTREGUE => 'ENTREGUE',
        ];
    }

    public static function toString($operacaoEstoque)
    {
        return self::toArray()[$operacaoEstoque];
    }

    public static function toEnum($conteudo)
    {
        foreach (self::toArray() as $operacao => $conteudoOperacao) {
            if ($conteudoOperacao == $conteudo) {
                return $operacao;
            }
        }
    }
}
