<?php

declare(strict_types=1);

namespace App\Presentation\Home;

use Nette\Application\UI\Presenter;
use App\Core\Slider\SliderModel;
use App\Core\Movies\MoviesModel;
use Nette\Application\UI\Form;


final class HomePresenter extends Presenter
{
    public function __construct(
        private SliderModel $sliderModel,
        private MoviesModel $MoviesModel
    ) {}

    public function renderDefault(): void
    {
        $this->template->slider = $this->sliderModel->getAll();
        $this->template->movies = $this->MoviesModel->getAll();
    }

    protected function createComponentSliderForm(): Form
    {
       $form = new Form;
        $form->addProtection();

        $form->addText('title', 'Název:')
    ->setRequired();

        $form->addText('description', 'Popis:')
            ->setRequired();

        $form->addUpload('image', 'Obrázek:')
    ->setRequired()
    ->addRule(Form::MIME_TYPE, 'Pouze JPG, PNG nebo GIF', [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
    ]);

        $form->addSubmit('send', 'Přidat slider');

        $form->onSuccess[] = function (Form $form, array $values) {

    $image = $values['image'];

    if ($image->isOk()) {

        // původní název souboru
        $imageName = $image->getName();

        // cílová cesta
        $targetPath = __DIR__ . '/../../../www/uploads/slider/' . $imageName;

        // (VOLITELNÉ) pokud existuje, přepiš
        if (file_exists($targetPath)) {
            unlink($targetPath);
        }

        $image->move($targetPath);

        $this->sliderModel->add(
            $values['title'],
            $values['description'],
            $imageName
        );
    }

    $this->redirect('this');
};

        return $form;
    }
}