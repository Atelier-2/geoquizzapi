<?php

namespace lbs\geoquizz\control;

use \Respect\Validation\Validator as v;

class PartieValidator
{
    public static function validators()
    {
        return
            [
                'id' => v::StringType()->alpha(),
                'mail' => v::email(),
                'livraison' => v::date('d-m-Y')->min('now'),
                "client_id" => v::optional(v::intVal()),
                "items" => v::arrayVal()->each(v::arrayVal()
                    ->key('uri', v::stringType())
                    ->key('q', v::intVal()))
            ];
    }
}