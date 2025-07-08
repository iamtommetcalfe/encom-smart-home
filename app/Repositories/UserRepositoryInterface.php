<?php

namespace App\Repositories;

interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * Find a user by email.
     *
     * @param string $email
     * @return \App\Models\User|null
     */
    public function findByEmail(string $email);
}
