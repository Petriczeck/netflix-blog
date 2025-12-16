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

    public function findById(int $id)
{
    return $this->database
        ->table('users')
        ->get($id);
}

    public function findByEmail(string $email)
{
    return $this->database
        ->table('users')
        ->where('email', $email)
        ->fetch();
}

public function findAll()
{
    return $this->database
        ->table('users')
        ->order('id');
}

public function delete(int $id): void
{
    $this->database
        ->table('users')
        ->where('id', $id)
        ->delete();
}

public function createAdminUser(string $email, string $password, string $role): void
{
    $this->database->table('users')->insert([
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role' => $role,
        'created_at' => new \DateTime(),
    ]);
}

public function update(
    int $id,
    string $email,
    ?string $password,
    string $role
): void {
    $data = [
        'email' => $email,
        'role' => $role,
    ];

    if ($password) {
        $data['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    $this->database
        ->table('users')
        ->where('id', $id)
        ->update($data);
}
}
