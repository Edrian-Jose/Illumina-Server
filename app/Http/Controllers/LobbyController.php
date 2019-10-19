<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Lobby;

class LobbyController extends Controller
{
    public function register(Request $request)
    {
        $lobby = null;
        $users = [];
        $user = User::where('username', $request['username'])->first();
        if ($user != null) {
            $lobby = Lobby::where('status', 0)->first();
            if ($lobby != null) {
                $users = json_decode($lobby->users);
                $count = count($users);
                array_push($users, $user);
                $lobby->users = json_encode($users);
                if ($count == 3) {
                    $lobby->status = 1;
                }
                $lobby->save();
                $lobby['users'] = $users;
                $lobby['response_message'] = $user->name . " had been successfully joined a lobby";
                $lobby['response_code'] = 1;
            } else {
                $lobby['hostid'] = $user->id;
                $lobby['status'] = 0;
                array_push($users, $user);
                $lobby['users'] = json_encode($users);
                Lobby::create($lobby);
                $lobby['users'] = $users;
                $lobby['response_message'] = $user->name . " had been created a lobby";
                $lobby['response_code'] = 2;
            }
        } else {
            $lobby['response_message'] = " User is not verified as a valid player";
            $lobby['response_code'] = 0;
        }

        return $lobby;
    }

    public function updatelobby(Request $request)
    {
        $host = $request['hostid'];
        $lobby = Lobby::where('hostid', $host)->where('status', 0)->first();
        if ($lobby != null) {
            $lobby['users'] = json_decode($lobby->users);
        }
        return $lobby;
    }

    public function ready(Request $request)
    {
        //
        $host = $request['hostid'];
        $lobby = Lobby::where('hostid', $host)->where('status', 1)->first();
        if ($lobby != null) {
            $oldcount = $lobby->readyplayers;
            $lobby->readyplayers = $oldcount + 1;
            $lobby->save();
        }
        return $lobby;
    }
}
