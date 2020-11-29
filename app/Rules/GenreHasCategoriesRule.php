<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Collection;

class GenreHasCategoriesRule implements Rule
{

    private $categoriesId;
    private $genresId;


    /**
     * GenreHasCategoriesRule constructor.
     * @param array $categoriesId
     */
    public function __construct($categoriesId)
    {
        $categoriesId = (is_array($categoriesId)) ? $categoriesId : [];
        $this->categoriesId = array_unique($categoriesId);
    }

    /**
     * @param string $attribute
     * @param mixed $genresId
     * @return bool
     */
    public function passes($attribute, $genresId)
    {
        $genresId = (is_array($genresId)) ? $genresId : [];

        $this->genresId = array_unique($genresId);

        if (!count($this->genresId) || !count($this->categoriesId)) {
            return false;
        }

        $categoriesFound = [];

        foreach ($this->genresId as $genreId) {
            $rows = $this->getRows($genreId);
            if (!$rows->count()) {
                return false;
            }

            array_push($categoriesFound, ...$rows->pluck('category_id')->toArray());
        }

        $categoriesFound = array_unique($categoriesFound);

        if (count($categoriesFound) !== count($this->categoriesId)) {
            return false;
        }

        return true;
    }

    /**
     * @param int $genreId
     * @return Collection
     */
    protected function getRows(string $genreId): Collection
    {
        return \DB::table('category_genre')
            ->where('genre_id', $genreId)
            ->whereIn('category_id', $this->categoriesId)
            ->get();
    }


    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Gênero não está relacionado a categoria.';
    }
}
