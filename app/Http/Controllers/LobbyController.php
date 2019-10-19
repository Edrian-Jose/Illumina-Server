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
        $userstatus = [];
        $user = User::where('username', $request['username'])->first();
        if ($user != null) {
            $lobby = Lobby::where('status', 0)->first();
            if ($lobby != null) {
                $users = json_decode($lobby->users, true);
                $count = count($users);
                $userstatus = json_decode($lobby->userstatus, true);
                $users[$user->username] = $user;
                $userstatus[$user->username] = 0;
                $lobby->users = json_encode($users);
                $lobby->userstatus = json_encode($userstatus);
                if ($count == 3) {
                    $lobby->status = 1;
                }
                $lobby->save();
                $lobby['users'] = array_values($users);
                $lobby['response_message'] = $user->name . " had been successfully joined a lobby";
                $lobby['response_code'] = 1;
            } else {
                $userstatus = [$user->username => 0];
                $lobby['hostid'] = $user->id;
                $lobby['status'] = 0;
                $users[$user->username] = $user;
                $lobby['users'] = json_encode($users);
                $lobby['userstatus'] = json_encode($userstatus);
                Lobby::create($lobby);
                $lobby['users'] = array_values($users);
                $lobby['response_message'] = $user->name . " had been created a lobby";
                $lobby['response_code'] = 2;
            }
        } else {
            $lobby['response_message'] = " User is not verified as a valid player";
            $lobby['response_code'] = 0;
        }

        return $lobby;
    }

    public function update($oldlobby)
    {
        $users = null;
        $host = $oldlobby['hostid'];
        $lobby = Lobby::where('hostid', $host)->where('status', 0)->first();
        if ($lobby != null) {
            $users = json_decode($lobby->users, true);
            $userstatus = json_decode($lobby->userstatus, true);
            foreach ($users as $key => $value) {
                if ($userstatus[$key] > 10) {
                    unset($userstatus[$key]);
                    unset($users[$key]);
                }
            }
            $lobby->users = json_encode($users);
            $lobby->userstatus = json_encode($userstatus);
            $lobby->save();
        }
        $lobby['users'] = array_values($users);;
        return $lobby;
    }

    public function WaitForSeconds($value)
    {
        $start = time();
        while (true) {
            if ((time() - $start) > $value) {
                return false;
            }
        }
    }

    public function updatelobby(Request $request)
    {
        $oldcount = $request->users;
        $newcount = 0;
        $lobby = null;
        do {
            $lobby = LobbyController::update($request);
            $users = $lobby['users'];
            $newcount = count($users);
            LobbyController::WaitForSeconds(2);
        } while ($newcount == $oldcount);

        return $lobby;
    }

    public function statuscheck(Request $request)
    {
        $username = $request->username;
        $hostid = $request->hostid;
        $lobby = Lobby::where('hostid', $hostid)->first();
        $userstatus = json_decode($lobby->userstatus, true);
        foreach ($userstatus as $key => $value) {
            if ($key == $username) {
                $userstatus[$username] = 0;
                continue;
            }
            $userstatus[$key] = $value + 1;
        }

        $lobby->userstatus = json_encode($userstatus);
        $lobby->save();
        return $request;
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
            if ($oldcount == 3) {
                //TODO: delete the lobby and create play
            }
        }
        return $lobby;
    }
}
