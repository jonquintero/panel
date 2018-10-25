<?php

namespace App\Http\Requests;

use App\Role;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'name' => 'required',
            'email' => [
                'required', 'email',
                Rule::unique('users')->ignore($this->user)
            ],
            'password' => '',
            'role' => [ Rule::in(Role::getList())],
            'bio' => 'required',
            'twitter' => ['nullable', 'present', 'url'],
            'profession_id' => [
                'nullable', 'present',
                Rule::exists('professions', 'id')->whereNull('deleted_at')
            ],
            'skills' => [
                'array',
                Rule::exists('skills', 'id'),
            ],
        ];
    }

    public function updateUser(User $user)
    {
        $user->forceFill([
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ]);

        if ($this->password != null) {
            $user->password = bcrypt($this->password);
        }

        //$user->update($data);

        $user->save();

        $user->profile->update([
            'twitter' => $this->twitter,
            'bio' => $this->bio,
            'profession_id' => $this->profession_id,
        ]);


        $user->skills()->sync($this->skills ?: []);



    }
}
