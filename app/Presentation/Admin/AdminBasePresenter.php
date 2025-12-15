<?php

declare(strict_types=1);

namespace App\Presentation\Admin;

use Nette\Application\UI\Presenter;

abstract class AdminBasePresenter extends Presenter
{
    protected function startup(): void
    {
        parent::startup();

        if (
            !$this->getUser()->isLoggedIn()
            || !$this->getUser()->isInRole('admin')
        ) {
            $this->flashMessage('Nemáš oprávnění vstoupit do administrace.', 'danger');
            $this->redirect('Home:default');
        }
    }
}