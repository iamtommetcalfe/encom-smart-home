<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * UserRepository constructor.
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return \App\Models\User|null
     */
    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }
}
