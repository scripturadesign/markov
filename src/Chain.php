<?php
/**
 * @package    Scriptura\Markov
 * @author     Martin Dilling-Hansen <martindilling@gmail.com>
 * @copyright  Copyright (c) 2016, Martin Dilling-Hansen
 * @license    http://opensource.org/licenses/MIT MIT License
 * @link       https://github.com/scripturadesign/markov
 */

namespace Scriptura\Markov;

class Chain
{
    /**
     * @var Tokenizer
     */
    private $tokenizer;

    /**
     * @var bool
     */
    private $needsRecalculation = true;

    /**
     * @var array
     */
    private $history = [];

    /**
     * @var array
     */
    private $matrix = [];

    /**
     * @param Tokenizer $tokenizer
     * @param array $history
     */
    public function __construct(Tokenizer $tokenizer, array $history = [])
    {
        $this->tokenizer = $tokenizer;
        $this->history = $history;

        $this->recalculateMatrix();
    }

    /**
     * Get the history of the training.
     *
     * @param string $key Get only a single entry with this key
     *
     * @return array
     */
    public function history($key = null)
    {
        if (is_null($key)) {
            return $this->history;
        }

        if (isset($this->history[$key])) {
            return $this->history[$key];
        }

        return [];
    }

    /**
     * Get the probability matrix calculated from the training.
     *
     * @param string $key Get only a single entry with this key
     *
     * @return array
     */
    public function matrix($key = null)
    {
        $this->recalculateMatrix();

        if (is_null($key)) {
            return $this->matrix;
        }

        if (isset($this->matrix[$key])) {
            return $this->matrix[$key];
        }

        return [];
    }

    /**
     * Train the chain with a given string.
     *
     * @param string $string
     */
    public function train($string)
    {
        $tokens = $this->tokenizer->tokenize($string);
        $count = count($tokens);

        for ($i = 1; $i < $count; $i++) {
            $matcher = $tokens[$i - 1];
            $state = $tokens[$i];

            if (!isset($this->history[$matcher][$state])) {
                $this->history[$matcher][$state] = 0;
            }

            $this->history[$matcher][$state]++;
        }

        $this->needsRecalculation = true;
    }

    /**
     * Recalculate the probability matrix if it needs recalculation.
     *
     * @return array
     */
    private function recalculateMatrix()
    {
        if (!$this->needsRecalculation) {
            return;
        }

        $this->matrix = [];

        foreach ($this->history as $states => $transitions) {
            $this->matrix[$states] = $this->calculateTransitionsProbability($transitions);
        }

        $this->needsRecalculation = false;
    }

    /**
     * Calculate probability for a list of transitions with their occurrence count.
     *
     * @param array $transitions
     *
     * @return array
     */
    private function calculateTransitionsProbability(array $transitions)
    {
        $probabilities = [];

        $total = array_sum($transitions);
        foreach ($transitions as $transition => $count) {
            $probabilities[$transition] = $count / $total;
        }

        return $probabilities;
    }
}
