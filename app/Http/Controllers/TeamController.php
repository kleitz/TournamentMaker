<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\teams;
use App\User;
use App\user_alert;
use Auth;
use Request;


class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $input = Request::all();

        $input['captain_id'] = Auth::user()->id;
        var_dump($input);
        teams::create($input);
        $team_id = teams::where('captain_id', '=', Auth::user()->id)->where('active', '=', 1)->get();
        foreach ($team_id as $value) {
            $user['team_id'] = $value->id;
        }

        $user = User::findorfail(Auth::user()->id);
        $user->team_id = $value->id;
        $user->save();

        return redirect('home');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($team_id)
    {
        $team = teams::findorfail($team_id);
        $input = Request::all();

        if (Auth::User()->id == $team->captain_id) {
            $team->name = $input['name'];
            $team->save();
            return redirect('home');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($user_id, $team_id)
    {
        $alerts = user_alert::all();
        foreach ($alerts as $alert) {
            if (($user_id == $alert->user_id) && ($team_id == $alert->team_id)) {
                $user = User::findorfail($user_id);
                $user->team_id = $team_id;
                $user->save();

                $alert->delete();
            }
        }
        return redirect('home');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */



    public function destroy($user_id)
    {
        $user = User::findorfail($user_id);
        $user->team_id = 0;
        $user->save();

        return redirect('home');
    }

    public function check()
    {
        $user_id = Auth::User()->id;
        return redirect('team/destroy/' . $user_id);
    }


    public function delete()
    {
        $users = User::all();
        $team = teams::findorfail(Auth::User()->team_id);

        foreach ($users as $user) {
            if ($user->team_id == $team->id) {
                $user->team_id = 0;
                $user->save();

                $input['user_id'] = $user->id;
                $input['alert_id'] = 5;
                $input['team_id'] = $team->id;

                user_alert::create($input);
            }
        }

        $team->active = false;
        $team->save();

        return redirect('home');
        
    }

    public function invite($user_id)
    {
        $user = User::findorfail($user_id);
        if ($user->team_id == 0) {
            $input['alert_id'] = 1;
            $input['user_id'] = $user_id;
            $input['team_id'] = Auth::User()->team_id;

            user_alert::create($input);
        }

        return redirect('home');
    }
}
