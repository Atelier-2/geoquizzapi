<?php

namespace lbs\geoquizz\control;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use GuzzleHttp\Client;
use \lbs\geoquizz\model\Serie as Serie;

class SeriesController
{
    protected $c;

    public function __construct(\Slim\Container $c = null)
    {
        $this->c = $c;
    }

    public function getSeries(Request $req, Response $resp, array $args)
    {
        try {
            $series = Serie::all();

            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');

            $rs->getBody()->write(json_encode([
                "series" => $series
                ]));

            return $rs;
        } catch (\Exception $e) {
            $rs = $resp->withStatus(404)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['Error_code' => 404, 'Error message' => "token no corresponding"]));
            return $rs;
        }
    }

    public function getSerie(Request $req, Response $resp, array $args)
    {
        $id = $args['id'];
        $serie = Serie::where('id', '=', $id)->get();
        if (!$serie->isEmpty()) {
            
            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode([
                "serie" => $serie
            ]));
            return $rs;
        } else {
            $rs = $resp->withStatus(404)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['Error_code' => 404, 'Error message' => "token no corresponding"]));
            return $rs;
        }
    }

    public function insertSerie(Request $req, Response $resp, array $args)
    {
        if ($req->getAttribute('has_errors')) {
            $errors = $req->getAttribute('errors');
            var_dump($errors);
        } else {  
            try {
                $body = $req->getParsedBody();
                $serie_ville = $body["ville"];
                $serie_map_refs = $body["map_refs"];

                $serie = new Serie();
                $serie->ville = $serie_ville;
                $serie->map_refs = $serie_map_refs;
                $serie->save();

                $rs = $resp->withStatus(201)
                    ->withHeader('Content-Type', 'application/json;charset=utf-8');
                $rs->getBody()->write(json_encode([
                    "message" => "ha funcionado este pedo"
                ]));
                return $rs;    
            } catch(\Exception $e) {
                $rs = $resp->withStatus(404)
                    ->withHeader('Content-Type', 'application/json;charset=utf-8');
                $rs->getBody()->write(json_encode([
                    'Error_code' => 404,
                    'Error_message' => $e
                ]));
                return $rs;
            }
        }    
    }

    public function updateSerie(Request $req, Response $resp, array $args)
    {
        if ($serie = Serie::find($args["id"])) {
            switch ($args["data"]) {
                case "ville":
                    $serie->ville = filter_var($args['value'], FILTER_SANITIZE_STRING);
                    $serie->save();
                    break;
                case "map_refs":
                    $serie->map_refs = filter_var($args['value'], FILTER_SANITIZE_STRING);
                    $serie->save();
                    break;
            }
            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode($serie));
            return $rs;
        } else {
            $rs = $resp->withStatus(404)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['Error_code' => 404, 'please enter an existing id']));
            return $rs;
        }
    }

    public function getSeriePhotos(Request $req, Response $resp, array $args)
    {
        $id = $args['id'];
        $series = Serie::where('id', '=', $id)->get();
        if (!$series->isEmpty()) {
            foreach ($series as $serie) {
                $serie_photos = $serie->getPhotos()->get();
            }
            
            foreach ($serie_photos as $photo) {
                $photos = array();
                $photos["description"] = $photo->description;
                $photos["long"] = $photo->long;
                $photos["lat"] = $photo->lat;
                $photos["url"] = $photo->url;
                $photos["id_serie"] = $photo->id_serie;
                $order["items"][] = $photos;
            }
            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode([
                "photos" => $order["items"]
            ]));
            return $rs;
        } else {
            $rs = $resp->withStatus(404)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['Error_code' => 404, 'Error message' => "token no corresponding"]));
            return $rs;
        }
    }
}