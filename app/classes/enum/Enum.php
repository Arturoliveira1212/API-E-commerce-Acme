<?php

namespace app\classes\enum;

interface Enum
{
    public static function ehValido($enum);
    public static function quantidadeOpcoes();
    public static function toString($enum);
    public static function toEnum($conteudo);
    public static function toArray();
}
