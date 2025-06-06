<?php

namespace app\classes\enum;

class OperacaoEstoque implements Enum
{
    public const ADICIONAR = 1;
    public const REMOVER = 2;

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
            self::ADICIONAR => 'ADICIONAR',
            self::REMOVER => 'REMOVER',
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
