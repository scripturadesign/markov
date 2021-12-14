<?php

namespace Scriptura\Markov\RNG;

interface RNG
{
    /**
     * Generates random integers within a range
     *
     * @param int $min The lowest value to be returned, which must be PHP_INT_MIN or higher.
     * @param int $max The highest value to be returned, which must be less than or equal to PHP_INT_MAX.
     * @return int Returns a random integer in the range min to max, inclusive.
     */
    public function between(int $min, int $max) : int;
}
