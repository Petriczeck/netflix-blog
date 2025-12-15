<?php

namespace App\Core\Slider;

use Nette\Database\Explorer;

class SliderModel
{
    public function __construct(
        private Explorer $database
    ) {}

    public function getAll()
    {
        return $this->database->table('slider');
    }

    public function add(string $title, string $description, string $image): void
{
    $this->database->table('slider')->insert([
        'title' => $title,
        'description' => $description,
        'image' => $image,
    ]);
}
}
