<?php

namespace App\Core\Categories;

use Nette\Database\Explorer;

class CategoriesModel
{
    public function __construct(
        private Explorer $database
    ) {}

    public function getAll()
    {
        return $this->database->table('categories');
    }
}