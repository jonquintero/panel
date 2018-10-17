<?php

namespace App\Http\Controllers;

use App\Profession;
use App\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = User::first(); //or auth()->user()

        return view('profile.edit',[
           'user' => $user,
           'professions' => Profession::orderBy('title')->get(),
        ]);
    }

    public function update(Request $request)
    {
        $user = User::first(); //or auth()->user()

        $data = $request->all();

      /*  if (empty($data['password'])) {
            unset($data['password']);
        } else {

            $data['password'] = bcrypt($data['password']);
        }*/



        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
        $user->profile->update([
            'bio' => $data['bio'],
            'twitter' => $data['twitter'],
            'profession_id' => $data['profession_id'],
        ]);


        return back();
    }
}
