<?php

namespace App\Service\Utils;

class PaginatorFactory
{
    /**
     * choicePaginator
     *
     * @return array
     */
    public function choicePaginator(): ?array
    {
        return [
            '5' => 5,
            '10' => 10,
            '15' => 15,
            '20' => 20,
            '30' => 30,
            '50' => 50,
            '100' => 100,
            '150' => 150,
            '200' => 200,
            '250' => 250,
            '300' => 300,
        ];
    }
}
