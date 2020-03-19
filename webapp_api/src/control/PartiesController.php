<?php

namespace lbs\geoquizz\control;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use GuzzleHttp\Client;
use \lbs\geoquizz\model\Partie as Partie;

class PartiesController
{
    protected $c;

    public function __construct(\Slim\Container $c = null)
    {
        $this->c = $c;
    }

    public function getParties(Request $req, Response $resp, array $args)
    {
        try {
            $parties = Partie::all();

            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');

            $rs->getBody()->write(json_encode([
                "type" => "collection",
                "parties" => $parties
                ]));

            return $rs;
        } catch (\Exception $e) {
            $rs = $resp->withStatus(404)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['Error_code' => 404, 'Error message' => "token no corresponding"]));
            return $rs;
        }
    }

    public function insertPartie(Request $req, Response $resp, array $args)
    {
        if ($req->getAttribute('has_errors')) {
            $errors = $req->getAttribute('errors');
            var_dump($errors);
        } else {  
            $body = $req->getParsedBody();
            $partie_nb_photos = $body["nb_photos"];
            $partie_status = $body["status"];
            $partie_score = $body["score"];
            $partie_joueur = $body["joueur"];
            $partie_serie_id = $body["id_serie"];

            $partie = new Partie();
            $partie->id = Uuid::uuid4();
            $token = random_bytes(32);
            $token = bin2hex($token);
            $partie->token = $token;
            $partie->nb_photos = $partie_nb_photos;
            $partie->status = $partie_status;
            $partie->score = $partie_score;
            $partie->joueur = $partie_joueur;
            $partie->id_serie = $partie_serie_id;
            $partie->save();

            $rs = $resp->withStatus(201)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode([
                "message" => "Vous venez de creer une nouvelle partie",
                "id" => $partie->id,
                "token" => $partie->token
            ]));
            return $rs;    
        } /*else {
            $rs = $resp->withStatus(404)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode([
                'Error_code' => 404,
                'Error_message' => "something went wrong"
            ]));
            return $rs;
        }*/
    }

    public function updatePartie(Request $req, Response $resp, array $args)
    {
        if ($partie = Partie::find($args["id"])) {
            switch ($args["data"]) {
                case "status":
                    if (filter_var($args['value'], FILTER_SANITIZE_STRING) == !0) {
                        $partie->status = filter_var($args['value'], FILTER_SANITIZE_STRING);
                        $partie->save();
                    } else {
                        echo "please use a valid status format";
                    }
                    break;
                case "score":
                    $partie->score = filter_var($args['value']);
                    $partie->save();
                    break;
            }
            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode($partie));
            return $rs;
        } else {
            $rs = $resp->withStatus(404)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['Error_code' => 404, 'please enter an existing id']));
            return $rs;
        }
    }
}