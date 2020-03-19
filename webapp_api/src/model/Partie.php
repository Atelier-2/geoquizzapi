<?php

namespace lbs\geoquizz\model;

class Partie extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'partie';
    protected $primary_key = 'id';

    public $incrementing = false;
    public $keyType = 'string';

    /*public function getItems()
    {
        return $this->hasMany('lbs\command\model\Item', 'command_id');
    }*/
}