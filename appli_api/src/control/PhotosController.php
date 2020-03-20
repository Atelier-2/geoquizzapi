<?php

namespace lbs\geoquizz\control;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \lbs\geoquizz\model\Photo as Photo;

class PhotosController
{
    protected $c;

    public function __construct(\Slim\Container $c = null)
    {
        $this->c = $c;
    }

    public function getPhotos(Request $req, Response $resp, array $args)
    {
        try {
            $photos = Photo::all();

            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');

            $rs->getBody()->write(json_encode([
                "photos" => $photos
                ]));

            return $rs;
        } catch (\Exception $e) {
            $rs = $resp->withStatus(404)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['Error_code' => 404, 'Error message' => $e]));
            return $rs;
        }
    }

    public function insertPhoto(Request $req, Response $resp, array $args)
    {
        if ($req->getAttribute('has_errors')) {
            $errors = $req->getAttribute('errors');
            var_dump($errors);
        } else {
            try {  
                $body = $req->getParsedBody();
                $photo_description = $body["description"];
                $photo_long = $body["long"];
                $photo_lat = $body["lat"];
                $photo_url = $body["url"];
                $photo_id_serie = $body["id_serie"];

                $photo = new Photo();
                $photo->description = $photo_description;
                $photo->long = $photo_long;
                $photo->lat = $photo_lat;
                $photo->url = $photo_url;
                $photo->id_serie = $photo_id_serie;
                $photo->save();

                $rs = $resp->withStatus(201)
                    ->withHeader('Content-Type', 'application/json;charset=utf-8');
                $rs->getBody()->write(json_encode([
                    "message" => "Vous venez d'ajouter une nouvelle photo",
                    "photo" => $photo
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
}