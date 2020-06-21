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

    public function __construct(int $order, array $history = [])
    {
        $this->order = $order;
        $this->history = $history;
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
     * Learn a single state transition.
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

    /**
     * Find a link by its state.
     *
     * @param array $state
     * @return \Scriptura\Markov\Link
     */
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
