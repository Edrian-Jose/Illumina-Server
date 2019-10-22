<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Lobby;
use App\LobbyRoom;

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
                $lobby['response_code'] = '1';
            } else {
                $userstatus = [$user->username => 0];
                $lobby['hostid'] = $user->id;
                $lobby['host'] = $user->username;
                $lobby['status'] = 0;
                $users[$user->username] = $user;
                $lobby['users'] = json_encode($users);
                $lobby['userstatus'] = json_encode($userstatus);
                Lobby::create($lobby);
                $lobby['users'] = array_values($users);
                $lobby['response_message'] = $user->name . " had been created a lobby";
                $lobby['response_code'] = '2';
            }
        } else {
            $lobby['response_message'] = " User is not verified as a valid player";
            $lobby['response_code'] = '0';
        }

        return $lobby;
    }

    public function update($oldlobby)
    {
        $response = '0';
        $users = null;
        $host = $oldlobby['hostid'];
        $lobby = Lobby::where('hostid', $host)->first();
        if ($lobby != null) {
            $users = json_decode($lobby->users, true);
            $userstatus = json_decode($lobby->userstatus, true);
            $hostdeleted = false;
            foreach ($users as $key => $value) {
                if ($hostdeleted) {
                    $lobby->host = $users[$key];
                    $hostdeleted = false;
                }
                if ($userstatus[$key] > 5) {
                    if ($lobby->host == $users[$key]) {
                        $hostdeleted = true;
                    }
                    unset($userstatus[$key]);
                    unset($users[$key]);
                    $response = '3';
                    if ($lobby->status == 1) {
                        $lobby->readyplayers = 0;
                        $response = '4';
                    }
                }
            }
            $lobby->users = json_encode($users);
            $lobby->userstatus = json_encode($userstatus);
            if (count($users) < 4) {
                $lobby->status = 0;
            }
            $lobby->save();
        }
        $lobby['users'] = array_values($users);
        $lobby['response_code'] == $response;
        return $lobby;
    }

    public function WaitForSeconds($value)
    {
        $start = microtime();
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
            usleep(250000);
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
        $lobby = Lobby::where('hostid', $host)->first();
        if ($lobby != null) {
            $oldcount = $lobby->readyplayers;
            if ($lobby->readyplayers < 4) {
                $lobby->readyplayers = $oldcount + 1;
                $lobby->save();
            }
        }
        return $lobby;
    }

    public function createplay(Request $request)
    {
        //$lobby = Lobby::where('hostid', $request['hostid'])->first();
        $players = [];
        foreach ($request['players'] as $key => $value) {
            $players[$key] = $value;
        }
        $turn = 0;
        $room['hostid'] = $request['hostid'];
        $room['turn'] = $turn;
        $room['players'] = json_encode($players);
        $room['data'] = json_encode($request['data']);
        LobbyRoom::create($room);
        //$lobby->delete();
        return $room;
    }
    public function waitplay(Request $request)
    {
        $hostid = $request['hostid'];
        do {
            $room = LobbyRoom::where('hostid', $request[$hostid])->first();
            usleep(250000);
        } while ($room == null);
        return $room;
    }
}
