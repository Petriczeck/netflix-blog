<?php

declare(strict_types=1);

namespace App\Presentation\Sign;

use Nette;
use Nette\Application\UI\Form;
use App\Core\User\UserRepository;
use Nette\Security\User;
use Nette\Security\AuthenticationException;

final class SignPresenter extends Nette\Application\UI\Presenter
{

    public function __construct(
        private UserRepository $userRepository,
) {}

    protected function createComponentSignInForm(): Form
    {
        $form = new Form;
        $form->getElementPrototype()->setAttribute('class', 'sign-in-form');

        $form->addEmail('email', 'Email:')
            ->setRequired('Zadej email');

        $form->addPassword('password', 'Heslo:')
            ->setRequired('Zadej heslo');

        $form->addSubmit('send', 'Přihlásit se');

        $form->onSuccess[] = [$this, 'signInFormSucceeded'];

        return $form;
    }

    public function signInFormSucceeded(Form $form, array $values)
{
    try {
       $this->user->login($values['email'], $values['password']);
        $this->flashMessage('Přihlášení proběhlo úspěšně 🎉', 'success');
        $this->redirect('Home:');
    } catch (AuthenticationException $e) {
        $form->addError('Špatný email nebo heslo');
    }
}

    protected function createComponentSignUpForm(): Form
{
    $form = new Form;
    $form->getElementPrototype()->setAttribute('class', 'sign-in-form');

    $form->addEmail('email', 'Email:')
        ->setRequired();

    $form->addPassword('password', 'Heslo:')
        ->setRequired();

    $form->addPassword('password2', 'Heslo znovu:')
        ->setRequired()
        ->addRule(Form::EQUAL, 'Hesla se neshodují', $form['password']);

    $form->addSubmit('send', 'Registrovat se');

    $form->onSuccess[] = [$this, 'signUpFormSucceeded'];

    return $form;
}

public function signUpFormSucceeded(Form $form, array $values): void
{
    // kontrola duplicity emailu
    if ($this->userRepository->findByEmail($values['email'])) {
        $form->addError('Uživatel s tímto e-mailem už existuje.');
        return;
    }

    $email = trim(mb_strtolower($values['email']));

$this->userRepository->create(
    $email,
    $values['password'] // 👈 ČISTÉ HESLO
);

    $this->flashMessage('Registrace proběhla úspěšně 🎉', 'success');
    $this->redirect('Sign:in');
}

public function actionOut(): void
{
    $this->user->logout();
    $this->flashMessage('Odhlášení proběhlo úspěšně 🎉', 'danger');
    $this->redirect('Sign:in');
}

}
