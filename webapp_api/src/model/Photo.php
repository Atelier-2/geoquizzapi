<?php

namespace lbs\geoquizz\model;

class Photo extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'photo';
    protected $primary_key = 'id';

    public $incrementing = true;
    public $keyType = 'string';
    public $timestamps = false;
}