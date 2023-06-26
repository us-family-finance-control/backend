<?php

namespace App\Repositories;

use App\Http\Requests\UserRegisterRequest;
use App\Models\Dependent;
use App\Models\Manager;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DependentRepository
{
    public function create(UserRegisterRequest $request): User
    {
        $dependent = Dependent::create([
            'manager_id' => Manager::findOrFail($request->manager_id)->id,
            'kinship' => $request->kinship
        ]);

        return User::create([
            'dependent_id' => $dependent->id,
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password)
        ]);
    }
}
