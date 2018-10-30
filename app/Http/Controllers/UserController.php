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
    public function trashed()
    {
        $users = User::onlyTrashed()->get();

        $title = 'Listado de usuarios en papelera';

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

    function destroy($id)
    {
       $user = User::onlyTrashed()->where('id', $id)->firstOrFail();
      //  abort_unless($user->trashed(), 400);

        $user->ForceDelete();

        return redirect()->route('users.trashed');
    }

    public function trash(User $user)
    {
        $user->delete();
        $user->profile()->delete();

        return redirect()->route('users.index');
    }
}
