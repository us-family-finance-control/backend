<?php

namespace App\Repositories;

use App\Http\Requests\UserRegisterRequest;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminRepository
{
    public function create(UserRegisterRequest $request): User
    {
        $admin = Admin::create();

        return User::create([
            'admin_id' => $admin->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
    }
}
