<?php
/**
 * Copyright (c) 2016 Martin Dilling-Hansen <martindilling@gmail.com>
 * https://github.com/scripturadesign/markov
 */

namespace Scriptura\Markov;

class Chain
{
    /**
     * @var bool
     */
    private $needsRecalculation = true;

    /**
     * @var int
     */
    private $order;

    /**
     * @var array
     */
    private $states;

    /**
     * @var array
     */
    private $transitions;

    /**
     * @var array
     */
    private $matrix = [];

    /**
     * @param int $order
     * @param array $history
     */
    public function __construct(int $order, array $history = [])
    {
        $this->order = $order;
        $this->states = $history['states'] ?? [];
        $this->transitions = $history['transitions'] ?? [];

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
            return [
                'states' => $this->states,
                'transitions' => $this->transitions,
            ];
        }

        $index = array_search($key, $this->states, true);

        if ($index === false) {
            return [
                'state' => [],
                'transitions' => [],
            ];
        }

        return [
            'state' => $this->states[$index],
            'transitions' => $this->transitions[$index],
        ];
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

        $index = array_search($key, $this->matrix['states'], true);

        if ($index === false) {
            return [
                'state' => [],
                'probabilities' => [],
            ];
        }

        return [
            'state' => $this->matrix['states'][$index],
            'probabilities' => $this->matrix['probabilities'][$index],
        ];
    }

    /**
     * Learn from an array of tokens.
     *
     * @param array $tokens
     */
    public function learn(array $tokens)
    {
        $tokens[] = '';
        $count = count($tokens);
        $state = array_fill(0, $this->order, '');

        for ($i = 0; $i < $count; ++$i) {
            $transition = $tokens[$i];

            $this->learnPart($state, $transition);

            array_shift($state);
            array_push($state, $transition);
        }

        $this->needsRecalculation = true;
    }

    /**
     * Learn from an array of tokens.
     *
     * @param array $state
     * @param string $transition
     */
    public function learnPart(array $state, $transition)
    {
        $key = array_search($state, $this->states);

        if ($key === false) {
            $this->states[] = $state;
            $key = count($this->states) - 1;
        }

        if (!isset($this->transitions[$key])) {
            $this->transitions[$key] = [];
        }

        if (!isset($this->transitions[$key][$transition])) {
            $this->transitions[$key][$transition] = 0;
        }


        ++$this->transitions[$key][$transition];
    }

    /**
     * Recalculate the probability matrix if it needs recalculation.
     *
     * @return array|void
     */
    private function recalculateMatrix()
    {
        if (!$this->needsRecalculation) {
            return;
        }

        $this->matrix = [];

        foreach ($this->states as $index => $state) {
            $transitions = $this->transitions[$index] ?? [];
            $this->matrix['states'][$index] = $state;
            $this->matrix['probabilities'][$index] = $this->calculateTransitionsProbability($transitions);
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
