<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Categories;

use App\Presentation\Admin\AdminBasePresenter;
use Nette\Application\UI\Form;
use Nette\Database\Explorer;
use App\Core\Categories\CategoriesModel;

final class CategoriesPresenter extends AdminBasePresenter
{
    public function __construct(
        private Explorer $database,
        private CategoriesModel $CategoriesModel
    ) {}

    public function renderDefault() {
        $this->template->categories = $this->CategoriesModel->getAll();
    }
}