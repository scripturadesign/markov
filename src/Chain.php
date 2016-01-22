<?php

namespace Scriptura\Markov;

class Chain
{
    /**
     * @var array
     */
    private $history;

    /**
     * @var array
     */
    private $matrix;

    public function __construct(array $history = [])
    {
        $this->history = $history;

        $this->matrix = $this->calculateProbabilies($history);
    }

    public function history()
    {
        return $this->history;
    }

    public function train(array $tokens)
    {
        for ($i = 1; $i < count($tokens); $i++) {

            $previous = $tokens[$i - 1];
            $current = $tokens[$i];

            if (!isset($this->history[$previous][$current])) {
                $this->history[$previous][$current] = 0;
            }

            $this->history[$previous][$current]++;
        }

        $this->matrix = $this->calculateProbabilies($this->history);
    }

    public function queryHistory($string)
    {
        return isset($this->history[$string]) ? $this->history[$string] : [];
    }

    public function matrix()
    {
        return $this->matrix;
    }

    private function calculateProbabilies(array $history)
    {
        $matrix = [];

        foreach ($history as $matcher => $states) {
            $matrix[$matcher] = $this->calculateMatcherProbability($states);
        }

        return $matrix;
    }

    private function calculateMatcherProbability(array $states)
    {
        $probabilities = [];

        $total = array_sum($states);
        foreach ($states as $state => $count) {
            $probabilities[$state] = $count / $total;
        }

        return $probabilities;
    }
}
