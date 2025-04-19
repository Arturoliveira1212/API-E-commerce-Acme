<?php

namespace app\classes;

class TipoPermissao
{
    public string $tipo;
    public $middleware;
    public $parametrosMiddleware;

    public function __construct(string $tipo, $middleware, ...$parametrosMiddleware)
    {
        $this->tipo = $tipo;
        $this->middleware = $middleware;
        $this->parametrosMiddleware = $parametrosMiddleware;
    }
}
