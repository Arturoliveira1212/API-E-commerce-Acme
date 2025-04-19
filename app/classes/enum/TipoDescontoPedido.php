<?php

namespace app\classes\enum;

class TipoDescontoPedido implements Enum
{
    public const DESCONTO_A_VISTA = 1;

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
            self::DESCONTO_A_VISTA => 'DESCONTO À VISTA'
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
