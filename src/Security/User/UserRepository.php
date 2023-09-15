<?php
declare(strict_types=1);

namespace App\Security\User;

interface UserRepository
{
    public function store(User $user): void;
    public function delete(User $user): void;
    public function find(string $identifier): ?User;
}