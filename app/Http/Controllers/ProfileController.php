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

       // $data = $request->all();

      /*  if (empty($data['password'])) {
            unset($data['password']);
        } else {

            $data['password'] = bcrypt($data['password']);
        }*/

        //unset($data['password']);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
        $user->profile->update([
            'bio' => $request->bio,
            'twitter' => $request->twitter,
            'profession_id' => $request->profession_id,
        ]);


        return back();
    }
}
