<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Middleware\Illumina;
use App\Http\Middleware\CheckGetTimeUrl;

use App\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->has("username") && $request->has("password") && $request->has("name") && $request->has("email")) {
            User::create($request->all());
            $request["response_message"] = "You are now registered " . $request["name"];
            return $request;
        } else {
            $request["response_message"] = "Incomplete information, cannot register";
            return $request;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //

    }

    public function login(Request $request)
    {
        $user = User::where("username", $request["username"])->first();

        if ($user == null) {
            $request["response_message"] = "Username doesn't exist";
        }

        $password = $user->password;
        if (Illumina::CompareIlluminaHashes($password, $request["password"])) {
            $user->logged_in = true;
            $user->save();
            return $user;
        } else {
            $request["response_message"] = "Username/password incorrect";
        }

        return $request;
    }

    public function exists(Request $request)
    {
        if (CheckGetTimeUrl::CheckUniqueKey($request)) {
            $name = "username";
            $value = "";
            if ($request->has("username")) {
                $name = "username";
                $value = $request["username"];
            }
            if ($request->has("email")) {
                $name = "email";
                $value = $request["email"];
            }
            $user = User::where($name, $value)->first();
            if ($user != null && $name == "username") {
                return "Username already exist";
            } else if ($user != null && $name == "email") {
                return "Email already exist";
            } else {
                return "";
            }
        }
        return redirect('/')->withException(InvalidArgumentException);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
