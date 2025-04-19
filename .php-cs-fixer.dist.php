<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__);

$config = new PhpCsFixer\Config();
$config->setRules([
    'array_syntax' => ['syntax' => 'short'], // Usar a sintaxe curta para arrays
    // 'braces' => [
    //     'position_after_functions_and_oop_constructs' => 'same', // <- ISSO AQUI
    // ],
    '@PSR12' => true, // Usando o padrão PSR-12
])
->setFinder($finder);

return $config;