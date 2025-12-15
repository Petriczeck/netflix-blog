<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Sliders;

use App\Presentation\Admin\AdminBasePresenter;
use App\Core\Slider\SliderModel;
use Nette\Application\UI\Form;
use Nette\Http\FileUpload;
use Nette\Utils\Strings;

final class SlidersPresenter extends AdminBasePresenter
{
    public function __construct(
        private SliderModel $sliderModel,
    ) {}

    public function renderDefault(): void
    {
        $this->template->sliders = $this->sliderModel->getAll();
    }

    public function renderCreate(): void
{
    // zatím nic – jen zobrazíme formulář
}

protected function createComponentSliderForm(): Form
{
    $form = new Form;

    $form->addText('title', 'Název:')
        ->setRequired();

    $form->addText('description', 'Popis:')
        ->setRequired();

    // ❗ už NENÍ required
    $form->addUpload('image', 'Nový obrázek (volitelné):')
        ->addRule(Form::IMAGE, 'Pouze JPG, PNG nebo GIF');

    $form->addSubmit('send', 'Uložit');

    $form->onSuccess[] = function (Form $form, array $values) {

    $idParam = $this->getParameter('id');

    // =========================
    // CREATE (bez ID)
    // =========================
    if ($idParam === null) {

        // obrázek je POVINNÝ
        if (!$values['image']->isOk()) {
            $form->addError('Vyber obrázek.');
            return;
        }

        $filename = $values['image']->getName();
        $values['image']->move(
            $this->getParameter('wwwDir') . '/uploads/slider/' . $filename
        );

        $this->sliderModel->add(
            $values['title'],
            $values['description'],
            $filename
        );

        $this->flashMessage('Slider byl vytvořen', 'success');
        $this->redirect('default');
    }

    // =========================
    // EDIT (s ID)
    // =========================
    $id = (int) $idParam;
    $slider = $this->sliderModel->getById($id);

    if (!$slider) {
        $this->error('Slider nenalezen');
    }

    $filename = $slider->image;

    // nový obrázek?
    if ($values['image']->isOk()) {

        $oldFile = $this->getParameter('wwwDir')
            . '/uploads/slider/' . $slider->image;

        if (is_file($oldFile)) {
            unlink($oldFile);
        }

        $filename = $values['image']->getName();
        $values['image']->move(
            $this->getParameter('wwwDir') . '/uploads/slider/' . $filename
        );
    }

    $this->sliderModel->update(
        $id,
        $values['title'],
        $values['description'],
        $filename
    );

    $this->flashMessage('Slider byl upraven', 'success');
    $this->redirect('default');
};


    return $form;
}


public function actionDelete(int $id): void
{
    $slider = $this->sliderModel->getById($id);

    if (!$slider) {
        $this->error('Slider nenalezen');
    }

    // smazání obrázku
    $filePath = $this->getParameter('wwwDir')
        . '/uploads/slider/' . $slider->image;

    if (is_file($filePath)) {
        unlink($filePath);
    }

    // smazání z DB
    $this->sliderModel->delete($id);

    $this->flashMessage('Slider byl smazán', 'success');
    $this->redirect('Sliders:default');
}

public function actionEdit(int $id): void
{
    $slider = $this->sliderModel->getById($id);

    if (!$slider) {
        $this->error('Slider nenalezen');
    }

    // předvyplnění formuláře
    $this['sliderForm']->setDefaults([
        'title' => $slider->title,
        'description' => $slider->description,
    ]);

    // pošleme slider do šablony (kvůli obrázku)
    $this->template->slider = $slider;
}

}