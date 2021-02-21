<?php

namespace App\Service\Utils;

class ReferenceFactory
{
    /**
     * n - Default number to create chain
     *
     * @var int
     */
    protected $n = 9;

    /**
     * generateReference
     *
     * @param  mixed $n
     * @return string
     */

    public function generateReference(?string $n = null): string
    {
        $this->n = (!isset($n)) ? $this->n : $n;
        $string = "";
        $chain = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        srand((float)microtime() * 1000000);
        for ($i = 0; $i < $this->n; $i++) {
            $string .= $chain[rand() % strlen($chain)];
        }
        return $string;
    }
}
