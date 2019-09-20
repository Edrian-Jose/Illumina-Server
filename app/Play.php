<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Play extends Model
{
    //
    public function players()
    {
        return $this->belongsToMany('App\Player', 'scores', 'play_id', 'player_id');
    }

    public function scores()
    {
        return $this->hasMany('App\Score');
    }
}
