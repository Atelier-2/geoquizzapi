<?php

namespace lbs\geoquizz\control;

use Firebase\JWT\JWT;
use lbs\geoquizz\model\User as user;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UsersController
{
    protected $c;

    public function __construct(\Slim\Container $c = null)
    {
        $this->c = $c;
    }

    public function authentifyUser(Request $req, Response $resp, array $args)
    {
        $mail = $req->getAttribute("mail");
        $password = $req->getAttribute("password");
        
        $user = new User();
        $users = User::where('mail', '=', $mail)->where('password', '=', $password)->get();
        foreach ($users as $user) {
            $user_id = $user->id;
        }
        if (!$users->isEmpty()) {
            $token = JWT::encode(
                ['iss' => 'http://api.backoffice.local',
                    'aud' => 'http://api.backoffice.local',
                    'iat' => time(),
                    'exp' => time() + 3600,
                    'uid' => $user_id,
                    'lvl' => 1],
                "secret", 'HS512');
            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode([
                "token" => $token
            ]));
            return $rs;
        } else {
            $rs = $resp->withStatus(401)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['type' => 'error', 'Error_code' => 401, 'message :' => 'email or password incorrect']));
            return $rs;
        }
    }
}