<?php

namespace lbs\geoquizz\control;

use Firebase\JWT\JWT;
use \lbs\common\bootstrap\Eloquent;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

class Middleware
{
    protected $c;

    public function __construct(\Slim\Container $c = null)
    {
        $this->c = $c;
    }

    function checkToken(Request $rq, Response $rs, callable $next)
    {
        if (!empty($rq->getQueryParams('token', null))) {
            $token = $rq->getQueryParams("token");
            $rq = $rq->withAttribute("token", $token);
            return $next($rq, $rs);
        } else {
            $rs = $rs->withStatus(401)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['type' => 'error', 'Error_code' => 401, 'message :' => 'no token found in URL']));
            return $rs;
        }
    }

    function getToken(Request $rq, Response $rs, callable $next)
    {
        $token = $rq->getAttribute("token");
        $token = $token["token"];
        $rq = $rq->withAttribute("token", $token);
        return $next($rq, $rs);
    }

    public function checkHeaderOrigin(Request $rq, Response $rs, callable $next)
    {
        if ($rq->getHeader('Origin')) {
            return $next($rq, $rs);
        } else {
            $rs = $rs->withStatus(401)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['type' => 'error', 'Error_code' => 401, 'message :' => 'no Origin Header found']));
            return $rs;
        }
    }

    public function headersCORS(Request $rq, Response $rs, callable $next)
    {
        $response = $next($rq, $rs)
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
        return $response;
    }
}
