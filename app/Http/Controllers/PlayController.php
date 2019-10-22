<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Lobby;
use App\PlayRoom;

class PlayController extends Controller
{
    public function savedata(Request $request)
    {
        $room = PlayRoom::where('hostid', $request['hostid'])->first();
        $lobby = Lobby::where('hostid', $request['hostid'])->first();
        if ($lobby != null) {
            $lobby->delete();
        }
        if ($room != null) {
            $room->data = json_encode($request['data']);
            $room->status = 1;
            $room->save();
        } else {
            $room = $request;
        }
        return $room;
    }
    public function loaddata(Request $request)
    {
        do {
            $room = PlayRoom::where('hostid', $request['hostid'])->first();
        } while ($room->status == 0);
        return $room;
    }
}
