<?php

require_once '../bootstrap.php';

use Slim\Factory\AppFactory;
use app\middlewares\ErrorHandlerMiddleware;
use Slim\Middleware\BodyParsingMiddleware;

$app = AppFactory::create();

$app->add( new BodyParsingMiddleware() );

$errorMiddleware = $app->addErrorMiddleware( true, true, true );
$errorMiddleware->setDefaultErrorHandler( new ErrorHandlerMiddleware() );

$rotas = glob( '../rotas/*.php' );
foreach( $rotas as $rota ){
    require_once $rota;
}

$app->run();