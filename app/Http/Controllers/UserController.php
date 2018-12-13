<?php

namespace App\Http\Controllers;

use App\{Profession, Skill, User, UserFilter};
use App\Http\Requests\{CreateUserRequest, UpdateUserRequest};
use Illuminate\Http\Request;


class UserController extends Controller
{
    public function index(Request $request, UserFilter $filters)
    {
        $users = User::query()
            ->with('team', 'skills', 'profile.profession')
            ->filterBy($filters, $request->only(['state', 'role','search']))
            ->orderByDesc('created_at')
            ->paginate();

        $users->appends($filters->valid());

        return view('users.index', [
            'users' => $users,
            'title' => 'Listado de usuarios',
          //  'roles' => trans('users.filters.roles'),
            'skills' => Skill::orderBy('name')->get(),
           // 'states' => trans('users.filters.states'),
            'view' => 'index',
            'checkedSkills' => collect(request('skills')),
        ]);
    }

    public function trashed()
    {
        $users = User::onlyTrashed()->paginate();

       // $title = 'Listado de usuarios en papelera';

        return view('users.index', [
            'users' => $users,
          //  'title' => $title,
            'view' => 'trash',
        ]);
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function create()
    {
        return $this->form('users.create', new User);
    }

    public function store(CreateUserRequest $request)
    {
        $request->createUser();

        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        return $this->form('users.edit', $user);
    }

    protected function form($view, User $user)
    {
        return view($view, [
            'professions' => Profession::orderBy('title', 'ASC')->get(),
            'skills' => Skill::orderBy('name', 'ASC')->get(),
          //  'roles' => trans('users.roles'),
            'user' => $user,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $request->updateUser($user);

        return redirect()->route('users.show', ['user' => $user]);
    }

    public function trash(User $user)
    {
        $user->delete();
        $user->profile()->delete();

        return redirect()->route('users.index');
    }

    public function destroy($id)
    {
        $user = User::onlyTrashed()->where('id', $id)->firstOrFail();

        $user->forceDelete();

        return redirect()->route('users.trashed');
    }
}
