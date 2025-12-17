<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Movies;

use App\Presentation\Admin\AdminBasePresenter;
use Nette\Application\UI\Form;
use Nette\Database\Explorer;
use App\Core\Movies\MoviesModel;
use App\Core\Categories\CategoriesModel;

final class MoviesPresenter extends AdminBasePresenter
{
    public function __construct(
        private Explorer $database,
        private MoviesModel $MoviesModel,
        private CategoriesModel $CategoriesModel
    ) {}

    public function renderDefault() {
        $this->template->movies = $this->MoviesModel->getAll();
    }

    public function renderCreate(): void
    {
        // zatím nic
    }

    protected function createComponentMovieForm(): Form
{
    $form = new Form;

    $form->addText('name', 'Name:')
        ->setRequired();

    $form->addTextArea('description', 'Description:')
        ->setRequired();

        $form->addText('categories', 'Kategorie (oddělené čárkou):')
    ->setRequired();

    $form->addUpload('image', 'Nový obrázek (volitelné):')
        ->addRule(Form::IMAGE, 'Pouze JPG, PNG nebo GIF');

    $form->addSubmit('send', 'Save');

    $form->onSuccess[] = function (Form $form, array $values) {

    $image = $values['image'];
    $filename = null;

    if ($image->hasFile()) {
        if (!$image->isOk()) {
            $form->addError('Chyba při nahrávání obrázku.');
            return;
        }

        $filename = $image->getSanitizedName();

        $image->move(
            $this->getParameter('wwwDir') . '/movies/' . $filename
        );
    }

    $this->MoviesModel->create(
        $values['name'],
        $values['description'],
        $filename,
        $values['categories'],
    );

    $this->flashMessage('Film byl vytvořen', 'success');
    $this->redirect('default');

    // uložení do DB
    $this->MoviesModel->create(
    $values['name'],
    $values['description'],
    $filename,
    $values['categories'],
);

    $this->flashMessage('Movie byla vytvořena.', 'success');
    $this->redirect('default');
};


    return $form;

    
}


}