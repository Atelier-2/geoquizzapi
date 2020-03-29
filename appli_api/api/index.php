<?php

require '../src/vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \lbs\common\bootstrap\Eloquent;
use \DavidePastore\Slim\Validation\Validation as Validation;

$config = parse_ini_file("conf/conf.ini");
$db = new Illuminate\Database\Capsule\Manager();
$db->addConnection($config);
$db->setAsGlobal();
$db->bootEloquent();

$errors = require 'conf/errors.php';
$configuration = new \Slim\Container(['settings' => ['displayErrorDetails' => true]]);
$app_config = array_merge($errors);
$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
        'debug' => true,
        'whoops.editor' => 'sublime',
    ]]);

$app->options('/{routes:.+}', function($request, $response, $args) {
    return $response;
})->add(lbs\geoquizz\control\Middleware::class . ':headersCORS');

$app->get('/photos[/]', function ($rq, $rs, $args) {
    return (new lbs\geoquizz\control\PhotosController($this))->getPhotos($rq, $rs, $args);
})->add(lbs\geoquizz\control\Middleware::class . ':headersCors');

$app->post('/photos[/]', function ($rq, $rs, $args) {
    return (new lbs\geoquizz\control\PhotosController($this))->insertPhoto($rq, $rs, $args);
})->add(lbs\geoquizz\control\Middleware::class . ':headersCors')->add(new Validation(lbs\geoquizz\validation\PhotoValidator::validators()));

$app->run();

