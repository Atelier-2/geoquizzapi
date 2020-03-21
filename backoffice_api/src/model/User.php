<?php

namespace lbs\geoquizz\model;

class User extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'user';
    protected $primary_key = 'id';

    public $incrementing = false;
    public $keyType = 'string';
    public $timestamps = false;
}