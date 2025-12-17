<?php

namespace App\Core\Movies;

use Nette\Database\Explorer;

class MoviesModel
{
    public function __construct(
        private Explorer $database
    ) {}

    public function getAll()
    {
        return $this->database->table('movies');
    }

  public function create(string $name, string $description, string $image, string $categories)
{
    return $this->database->table('movies')->insert([
        'name' => $name,
        'description' => $description,
        'image' => $image,
        'categories' => $categories,
    ]);
}

   

}
