<?php

namespace Scriptura\Markov\RNG;

class RandomIntRNG implements RNG
{
    public function between(int $min, int $max) : int
    {
        try {
            return random_int($min, $max);
        } catch (\Exception $e) {
            return 0;
        }
    }
}
