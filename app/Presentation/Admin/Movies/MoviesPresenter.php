<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Movies;

use App\Presentation\Admin\AdminBasePresenter;
use Nette\Application\UI\Form;
use Nette\Database\Explorer;
use App\Core\Movies\MoviesModel;

final class MoviesPresenter extends AdminBasePresenter
{
    public function __construct(
        private Explorer $database,
        private MoviesModel $MoviesModel
    ) {}

    public function renderDefault() {
        $this->template->movies = $this->MoviesModel->getAll();
    }
}