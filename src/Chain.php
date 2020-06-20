<?php
declare(strict_types=1);

namespace Scriptura\Markov;

class Chain
{
    private int $order;
    /**
     * @var Link[]
     */
    private array $history;
    private array $matrix = [];

    public function __construct(int $order, array $history = [])
    {
        $this->order = $order;
        $this->history = $history;
    }

    /**
     * Get the states in this chain.
     *
     * @return array
     */
    public function states() : array
    {
        return [];
    }

    /**
     * Get the history of the training.
     *
     * @return array
     */
    public function history() : array
    {
        return $this->history;
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
    public function learn(array $tokens) : void
    {
        $tokens[] = '';
        $count = count($tokens);
        $state = array_fill(0, $this->order, '');

        for ($i = 0; $i < $count; ++$i) {
            $transition = $tokens[$i];

            $this->learnPart($state, $transition);

            // Remove head from state
            array_shift($state);
            // Push next to tail of state
            array_push($state, $transition);
        }
    }

    /**
     * Learn from an array of tokens.
     *
     * @param array $state
     * @param string $transition
     */
    public function learnPart(array $state, $transition) : void
    {
        $link = $this->find($state);

        if ($link->isNull()) {
            $link = new Link($state);
            $this->history[] = $link;
        }

        $link->add($transition);
    }

    public function find(array $state) : Link
    {
        foreach ($this->history as $link) {
            if ($link->state() === $state) {
                return $link;
            }
        }

        return Link::null();
    }
}
