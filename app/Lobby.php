<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lobby extends Model
{
    protected $fillable = [
        'status', 'hostid', 'users', 'readyplayers', 'userstatus'
    ];
}
