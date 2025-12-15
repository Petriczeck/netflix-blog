<?php

namespace App\Core\User;

use Nette\Database\Explorer;
use Nette\Security\SimpleIdentity;
use Nette\Security\IIdentity;
use Nette\Security\AuthenticationException;
use Nette\Security\Authenticator;

class UserAuthenticator implements Authenticator
{
    public function __construct(
        private Explorer $database
    ) {}

    public function authenticate(string $email, string $password): IIdentity
    {
        $email = trim(mb_strtolower($email));

$row = $this->database->table('users')
    ->where('email', $email)
    ->fetch();

        if (!$row || !password_verify($password, $row->password)) {
            throw new AuthenticationException('Neplatné přihlašovací údaje');
        }

        return new SimpleIdentity(
    $row->id,
    $row->role ?: 'user', // 👈 fallback
    [
        'email' => $row->email,
    ]
);
    }
}
