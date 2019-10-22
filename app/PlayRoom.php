<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlayRoom extends Model
{
    protected $fillable = [
        'hostid', 'host', 'status', 'users', 'turn', 'users', 'data'
    ];
}
