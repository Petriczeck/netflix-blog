<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Sliders;

use App\Presentation\Admin\AdminBasePresenter;
use App\Core\Slider\SliderModel;

final class SlidersPresenter extends AdminBasePresenter
{
    public function __construct(
        private SliderModel $sliderModel,
    ) {}

    public function renderDefault(): void
    {
        $this->template->sliders = $this->sliderModel->getAll();
    }
}