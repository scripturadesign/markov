<?php

namespace Scriptura\Markov;

use Scriptura\Markov\RNG\RNG;

class Generator
{
    private RNG $rng;
    private Chain $chain;

    public function __construct(RNG $rng, Chain $chain)
    {
        $this->rng = $rng;
        $this->chain = $chain;
    }

    public function generate() : array
    {
        $result = [];
        $state = array_fill(0, $this->chain->order(), '');
        $stop = false;

        while (! $stop) {
            $link = $this->chain->find($state);
            $last = $this->next($link);

            // Remove head from state
            array_shift($state);
            // Push next to tail of state
            $state[] = $last;

            if ($last === '') {
                $stop = true;

                continue;
            }

            // Only add to result when we know it's not the last empty string
            $result[] = $last;
        }

        return $result;
    }

    public function next(Link $link) : string
    {
        $rand = $this->rng->between(0, array_sum($link->transitions()));

        foreach ($link->transitions() as $key => $value) {
            $rand -= $value;
            if ($rand <= 0) {
                return $key;
            }
        }

        return '';
    }
}
