<?php

namespace App\Http\Controllers;

use App\{Http\Forms\UserForm,
    Http\Requests\CreateUserRequest,
    Http\Requests\UpdateUserRequest,
    Profession,
    Skill,
    User,
    UserProfile};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        $title = 'Listado de usuarios';

        return view('users.index', compact('title', 'users'));
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function create()
    {
        $user = new User;

        return new UserForm('users.create', new User);

    }

    public function store(CreateUserRequest $request)
    {
        $request->createUser();

        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        return new UserForm('users.edit', $user);
       // return view('users.edit', compact('user'))->with($this->formsData());
    }


    public function update(UpdateUserRequest $request, User $user)
    {

        $request->updateUser($user);


        return redirect()->route('users.show', ['user' => $user]);
    }

    function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index');
    }
}
