<?php

namespace lbs\geoquizz\model;

class Serie extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'serie';
    protected $primary_key = 'id';

    public $incrementing = false;
    public $keyType = 'string';
    public $timestamps = false;

    public function getPhotos()
    {
        return $this->hasMany('lbs\geoquizz\model\Photo', 'id_serie');
    }
}