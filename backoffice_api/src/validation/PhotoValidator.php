<?php

namespace lbs\geoquizz\validation;

use \Respect\Validation\Validator as v;

class PhotoValidator
{
    public static function validators()
    {
        return
            [
                'description' => v::StringType()->alpha(),
                'long' => v::floatVal(),
                'lat' => v::floatVal(),
                "url" => v::StringType(),
                "id_serie" => v::intVal()
            ];
    }
}
