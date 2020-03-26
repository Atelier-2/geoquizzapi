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

$app->get('/series[/]', function ($rq, $rs, $args) {
    return (new lbs\geoquizz\control\SeriesController($this))->getSeries($rq, $rs, $args);
})->add(lbs\geoquizz\control\Middleware::class . ':headersCors')->add(lbs\geoquizz\control\Middleware::class . ':checkHeaderOrigin')->add(lbs\geoquizz\control\Middleware::class . ':decodeJWT')->add(lbs\geoquizz\control\Middleware::class . ':checkJWT');

$app->get('/serie/{id}', function ($rq, $rs, $args) {
    return (new lbs\geoquizz\control\SeriesController($this))->getSerie($rq, $rs, $args);
})->add(lbs\geoquizz\control\Middleware::class . ':headersCors')->add(lbs\geoquizz\control\Middleware::class . ':checkHeaderOrigin')->add(lbs\geoquizz\control\Middleware::class . ':decodeJWT')->add(lbs\geoquizz\control\Middleware::class . ':checkJWT');

$app->get('/serie/{id}/photos[/]', function ($rq, $rs, $args) {
    return (new lbs\geoquizz\control\SeriesController($this))->getSeriePhotos($rq, $rs, $args);
})->add(lbs\geoquizz\control\Middleware::class . ':headersCORS')->add(lbs\geoquizz\control\Middleware::class . ':checkHeaderOrigin')->add(lbs\geoquizz\control\Middleware::class . ':decodeJWT')->add(lbs\geoquizz\control\Middleware::class . ':checkJWT');

$app->post('/series[/]', function ($rq, $rs, $args) {
    return (new lbs\geoquizz\control\SeriesController($this))->insertSerie($rq, $rs, $args);
})->add(lbs\geoquizz\control\Middleware::class . ':headersCors')->add(lbs\geoquizz\control\Middleware::class . ':checkHeaderOrigin')->add(new Validation(lbs\geoquizz\validation\SerieValidator::validators()))->add(lbs\geoquizz\control\Middleware::class . ':decodeJWT')->add(lbs\geoquizz\control\Middleware::class . ':checkJWT');

$app->put('/serie/{id}/{data}/{value}', function ($rq, $rs, $args) {
    return (new lbs\geoquizz\control\SeriesController($this))->updateSerie($rq, $rs, $args);
})->add(lbs\geoquizz\control\Middleware::class . ':headersCors')->add(lbs\geoquizz\control\Middleware::class . ':checkHeaderOrigin')->add(lbs\geoquizz\control\Middleware::class . ':decodeJWT')->add(lbs\geoquizz\control\Middleware::class . ':checkJWT');

$app->post('/photos[/]', function ($rq, $rs, $args) {
    return (new lbs\geoquizz\control\PhotosController($this))->insertPhoto($rq, $rs, $args);
})->add(lbs\geoquizz\control\Middleware::class . ':headersCors')->add(lbs\geoquizz\control\Middleware::class . ':checkHeaderOrigin')->add(new Validation(lbs\geoquizz\validation\PhotoValidator::validators()))->add(lbs\geoquizz\control\Middleware::class . ':decodeJWT')->add(lbs\geoquizz\control\Middleware::class . ':checkJWT');

$app->get('/photos[/]', function ($rq, $rs, $args) {
    return (new lbs\geoquizz\control\PhotosController($this))->getPhotos($rq, $rs, $args);
})->add(lbs\geoquizz\control\Middleware::class . ':headersCors')->add(lbs\geoquizz\control\Middleware::class . ':checkHeaderOrigin')->add(lbs\geoquizz\control\Middleware::class . ':decodeJWT')->add(lbs\geoquizz\control\Middleware::class . ':checkJWT');

$app->post('/user/auth[/]', function ($rq, $rs, $args) {
    return (new lbs\geoquizz\control\UsersController($this))->authentifyUser($rq, $rs, $args);
})->add(lbs\geoquizz\control\Middleware::class . ':headersCORS')->add(lbs\geoquizz\control\Middleware::class . ':checkHeaderOrigin')->add(lbs\geoquizz\control\Middleware::class . ':decodeAuthorization')->add(lbs\geoquizz\control\Middleware::class . ':checkAuthorization');

$app->run();