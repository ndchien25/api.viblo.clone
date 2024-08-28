<?php

namespace App\Repositories\User;

use App\Repositories\BaseRepository;
use App\Models\User;

class UserRepository extends BaseRepository implements IUserRepository
{
    /**
     * Set Model for Class
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

}
