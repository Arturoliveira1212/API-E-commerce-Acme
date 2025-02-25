<?php

require_once '../bootstrap.php';

use Slim\Factory\AppFactory;
use app\middlewares\ErrorHandlerMiddleware;
use Slim\Middleware\BodyParsingMiddleware;

$app = AppFactory::create();

$app->add( new BodyParsingMiddleware() );

$errorMiddleware = $app->addErrorMiddleware( true, true, true );
$errorMiddleware->setDefaultErrorHandler( new ErrorHandlerMiddleware() );

require_once '../app/rotas/rotas.php';

$app->run();