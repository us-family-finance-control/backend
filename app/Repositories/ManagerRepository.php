<?php

namespace App\Repositories;

use App\Http\Requests\UserRegisterRequest;
use App\Models\Manager;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ManagerRepository
{
    public function create(UserRegisterRequest $request): User
    {
        $manager = Manager::create([
            'phone' => $request->get('phone')
        ]);

        return User::create([
            'manager_id' => $manager->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
    }
}
