<?php

namespace Scriptura\Markov\RNG;

class DeterministicRNG implements RNG
{
    public int $determinedResult = 0;

    public function between(int $min, int $max) : int
    {
        if ($this->determinedResult < $min) {
            return $min;
        }

        if ($this->determinedResult > $max) {
            return $max;
        }

        return $this->determinedResult;
    }
}
