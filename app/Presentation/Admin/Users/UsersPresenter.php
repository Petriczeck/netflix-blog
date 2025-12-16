<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Users;

use App\Presentation\Admin\AdminBasePresenter;
use App\Core\User\UserRepository;
use Nette\Application\UI\Form;

final class UsersPresenter extends AdminBasePresenter
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function renderDefault(): void
    {
        $this->template->users = $this->userRepository->findAll();
    }

    public function renderCreate(): void
{
    // jen zobrazí formulář
}

    public function actionDelete(int $id): void
{
    // ❌ admin si nesmí smazat sám sebe
    if ($id === (int) $this->user->getId()) {
        $this->flashMessage('Nemůžeš smazat sám sebe.', 'danger');
        $this->redirect('default');
    }

    $user = $this->userRepository->findById($id);

    if (!$user) {
        $this->error('Uživatel nenalezen');
    }

    $this->userRepository->delete($id);

    $this->flashMessage('Uživatel byl smazán', 'success');
    $this->redirect('default');
}

protected function createComponentUserForm(): Form
{
    $form = new Form;

    $form->addEmail('email', 'Email:')
        ->setRequired('Zadej email');

    $form->addPassword('password', 'Heslo:')
        ->setNullable() // ❗ není povinné
        ->addRule(Form::MIN_LENGTH, 'Heslo musí mít alespoň 6 znaků', 6);

    $form->addSelect('role', 'Role:', [
        'user' => 'User',
        'admin' => 'Admin',
    ])->setRequired();

    $form->addSubmit('send', 'Uložit');

    $form->onSuccess[] = function (Form $form, array $values) {

        $idParam = $this->getParameter('id');

        // =====================
        // CREATE
        // =====================
        if ($idParam === null) {

            if ($this->userRepository->findByEmail($values['email'])) {
                $form->addError('Uživatel s tímto e-mailem už existuje.');
                return;
            }

            $this->userRepository->createAdminUser(
                $values['email'],
                $values['password'],
                $values['role']
            );

            $this->flashMessage('Uživatel byl vytvořen.', 'success');
            $this->redirect('default');
        }

        // =====================
        // EDIT
        // =====================
        $id = (int) $idParam;
        $user = $this->userRepository->findById($id);

        if (!$user) {
            $this->error('Uživatel nenalezen');
        }

        // ❌ admin si nesmí změnit roli sám sobě
        if ($id === (int) $this->user->getId()) {
            $values['role'] = $user->role;
        }

        $this->userRepository->update(
            $id,
            $values['email'],
            $values['password'], // může být NULL
            $values['role']
        );

        $this->flashMessage('Uživatel byl upraven.', 'success');
        $this->redirect('default');
    };

    return $form;
}


public function actionEdit(int $id): void
{
    $editedUser = $this->userRepository->findById($id);

    if (!$editedUser) {
        $this->error('Uživatel nenalezen');
    }

    // ❌ admin si nesmí změnit roli sám sobě
    if ($editedUser->id === (int) $this->user->getId()) {
        $this->template->disableRoleChange = true;
    } else {
        $this->template->disableRoleChange = false;
    }

    $this['userForm']->setDefaults([
        'email' => $editedUser->email,
        'role' => $editedUser->role,
    ]);

    $this->template->editedUser = $editedUser;
}

}