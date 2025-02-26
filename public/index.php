<?php

require_once '../bootstrap.php';

use Slim\Factory\AppFactory;
use app\middlewares\ErrorHandlerMiddleware;
use app\middlewares\SanitizacaoDadosMiddleware;
use Slim\Middleware\BodyParsingMiddleware;

$app = AppFactory::create();

$app->add( new BodyParsingMiddleware() );
$app->add( new SanitizacaoDadosMiddleware() );

$errorMiddleware = $app->addErrorMiddleware( true, true, true );
$errorMiddleware->setDefaultErrorHandler( new ErrorHandlerMiddleware() );

const CONTENT_TYPE = 'application/json';

$rotas = glob( '../rotas/*.php' );
foreach( $rotas as $rota ){
    require_once $rota;
}

$app->run();