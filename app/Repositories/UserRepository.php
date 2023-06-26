<?php

namespace App\Repositories;

use App\Http\Requests\UserRegisterRequest;
use App\Models\User;

class UserRepository
{
    public function create(UserRegisterRequest $request): User
    {
        $type = $request->type;

        switch ($type) {
            case User::TYPE_ADMIN:
                return (new AdminRepository())->create($request);
                break;

            case User::TYPE_MANAGER:
                return (new ManagerRepository())->create($request);
                break;

            case User::TYPE_DEPENDENT:
                return (new DependentRepository())->create($request);
                break;
        }
    }
}
