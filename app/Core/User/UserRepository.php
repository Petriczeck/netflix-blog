<?php

declare(strict_types=1);

namespace App\Core\User;

use Nette\Database\Explorer;
use Nette\Security\Passwords;

final class UserRepository
{
    public function __construct(
        private Explorer $database,
        private Passwords $passwords,
    ) {}

    public function create(string $email, string $password): void
    {
    $this->database->table('users')->insert([
        'email' => $email,
        'password' => $this->passwords->hash($password), // 👈 JEDINÉ HASHOVÁNÍ
        'created_at' => new \DateTime(),
    ]);
}

    public function findByEmail(string $email)
{
    return $this->database
        ->table('users')
        ->where('email', $email)
        ->fetch();
}
}
